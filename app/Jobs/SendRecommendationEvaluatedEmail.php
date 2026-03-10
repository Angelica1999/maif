<?php

namespace App\Jobs;

use App\Models\Recommendation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PHPMailer\PHPMailer\PHPMailer;
use Exception;

class SendRecommendationEvaluatedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recommendation;

    public function __construct(Recommendation $recommendation)
    {
        $this->recommendation = $recommendation;
    }

    public function handle(): void
    {
        try {
            $this->sendMail(
                $this->recommendation->email,
                $this->recommendation
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send recommendation evaluated email: ' . $e->getMessage());
        }
    }

    private function sendMail($recipientEmail, $recommendation)
    {
        try {
            $email_doh      = config('mail.username');
            $email_password = config('mail.password');

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $email_doh;
            $mail->Password   = $email_password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->SMTPDebug  = 0;

            // Recipients
            $mail->setFrom($email_doh, 'DOH-CVCHD MAIFIP');
            $mail->addAddress($recipientEmail);
            $mail->addReplyTo($email_doh);

            // Content
            $mail->isHTML(true);

            $typeLabel = $recommendation->type == 'bug' ? 'Bug Report' : 'Recommendation';

            $mail->Subject = 'Your ' . $typeLabel . ' Has Been Evaluated';

            $mail->addEmbeddedImage(public_path('images/maipp_banner.png'), 'unique_cid_for_image', 'image.jpg');

            // Status styling
            $statusColors = [
                'approved' => '#1ca75e',
                'rejected' => '#dc3545',
                'pending'  => '#e08b00',
            ];
            $statusLabels = [
                'approved' => '✅ Approved',
                'rejected' => '❌ Rejected',
                'pending'  => '⏳ Pending',
            ];
            $statusColor = $statusColors[$recommendation->status] ?? '#6c757d';
            $statusLabel = $statusLabels[$recommendation->status] ?? ucfirst($recommendation->status);

            $mail->Body = '
            <html>
            <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
                <div style="max-width: 600px; margin: 0 auto; padding: 20px;">

                    <div style="text-align: center; margin-bottom: 20px;">
                        <img src="cid:unique_cid_for_image" style="max-width: 100%; height: auto;">
                    </div>

                    <div style="background-color: #f8f9fa; border-left: 4px solid ' . $statusColor . '; padding: 15px; margin-bottom: 20px;">
                        <h2 style="color: ' . $statusColor . '; margin-top: 0;">Your ' . $typeLabel . ' Has Been Evaluated</h2>

                        <p>Dear ' . $recommendation->email . ',</p>
                        <p>Good day!</p>

                        <p>We would like to inform you that your submitted ' . strtolower($typeLabel) . ' has been reviewed and evaluated by our team.</p>

                        <!-- Status -->
                        <table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
                            <tr>
                                <td style="padding: 10px 14px; background: #fff; border: 1px solid #dee2e6; font-weight: bold; width: 35%;">Status</td>
                                <td style="padding: 10px 14px; background: #fff; border: 1px solid #dee2e6;">
                                    <span style="color: ' . $statusColor . '; font-weight: bold;">' . $statusLabel . '</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 14px; background: #fff; border: 1px solid #dee2e6; font-weight: bold;">Your Submission</td>
                                <td style="padding: 10px 14px; background: #fff; border: 1px solid #dee2e6;">' . nl2br(e($recommendation->recommendation)) . '</td>
                            </tr>';

           
            if ($recommendation->remarks) {
                $mail->Body .= '
                            <tr>
                                <td style="padding: 10px 14px; background: #fff; border: 1px solid #dee2e6; font-weight: bold;">Admin Remarks</td>
                                <td style="padding: 10px 14px; background: #fff; border: 1px solid #dee2e6;">' . nl2br(e($recommendation->remarks)) . '</td>
                            </tr>';
            }

           
            if ($recommendation->evaluated_by) {
                $mail->Body .= '
                            <tr>
                                <td style="padding: 10px 14px; background: #fff; border: 1px solid #dee2e6; font-weight: bold;">Evaluated By</td>
                                <td style="padding: 10px 14px; background: #fff; border: 1px solid #dee2e6;">' . e($recommendation->evaluated_by) . '</td>
                            </tr>';
            }

            $mail->Body .= '
                        </table>';

            // Message based on status
            if ($recommendation->status === 'approved') {
                $mail->Body .= '
                        <p>We are pleased to inform you that your ' . strtolower($typeLabel) . ' has been <strong style="color: ' . $statusColor . ';">approved</strong>. Thank you for your valuable input — it will be taken into consideration as we continue to improve our system.</p>';
            } elseif ($recommendation->status === 'rejected') {
                $mail->Body .= '
                        <p>After careful review, your ' . strtolower($typeLabel) . ' was <strong style="color: ' . $statusColor . ';">not approved</strong> at this time. We appreciate your effort in reaching out and encourage you to continue sharing your feedback.</p>';
            } else {
                $mail->Body .= '
                        <p>Your ' . strtolower($typeLabel) . ' is currently still <strong style="color: ' . $statusColor . ';">under review</strong>. We will notify you once a final decision has been made.</p>';
            }

            $mail->Body .= '
                        <p>Thank you for your continued support and cooperation.</p>

                        <p>Sincerely,</p>
                        <p><strong>DOH-CVCHD MAIFIP Team</strong></p>

                        <hr style="border: none; border-top: 1px solid #dee2e6; margin: 20px 0;">

                        <p style="font-size: 12px; color: #6c757d; text-align: center;">
                            This is an automated message from DOH-CVCHD MAIFIP System. Please do not reply to this email.
                        </p>
                    </div>
                </div>
            </body>
            </html>';

            return $mail->send();

        } catch (Exception $e) {
            \Log::error('PHPMailer Error (Evaluated): ' . $e->getMessage());
            return false;
        }
    }
}