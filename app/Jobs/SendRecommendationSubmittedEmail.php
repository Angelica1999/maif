<?php

namespace App\Jobs;

use App\Models\Recommendation;
use App\Models\MailHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Exception;

class SendRecommendationSubmittedEmail implements ShouldQueue
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
                $this->recommendation,
                'submitted'
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send recommendation submitted email: ' . $e->getMessage());
        }
    }

    private function sendMail($recipientEmail, $recommendation, $type)
    {
        try {
            $email_doh = config('mail.username');
            $email_password = config('mail.password');
            
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $email_doh;
            $mail->Password   = $email_password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->SMTPDebug = 0;

            // Recipients
            $mail->setFrom($email_doh, 'DOH-CVCHD MAIFIP');
            $mail->addAddress($recipientEmail);
            $mail->addReplyTo($email_doh);

            // Content
            $mail->isHTML(true);
            
            $typeLabel = $recommendation->type == 'bug' ? 'Bug Report' : 'Recommendation';
            $mail->Subject = 'Your ' . $typeLabel . ' Has Been Received';
            
            $mail->addEmbeddedImage(public_path('images/maipp_banner.png'), 'unique_cid_for_image', 'image.jpg');

            $statusColor = $recommendation->type == 'bug' ? '#dc3545' : '#0d6efd';
            
            $mail->Body = '
            <html>
            <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
                <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <img src="cid:unique_cid_for_image" style="max-width: 100%; height: auto;"> 
                    </div>
                    
                    <div style="background-color: #f8f9fa; border-left: 4px solid ' . $statusColor . '; padding: 15px; margin-bottom: 20px;">
                        <h2 style="color: ' . $statusColor . '; margin-top: 0;">Thank You for Your ' . $typeLabel . '</h2>
                        
                        <p>Dear ' . $recommendation->email . ',</p>
                        <p>Good day!</p>';

                    // Conditional message based on type
                    if ($recommendation->type == 'bug') {
                        $mail->Body .= '
                                <p>Thank you for reporting the identified bugs and system issues. We highly appreciate your feedback, as it helps us improve the quality and reliability of our system. Rest assured that these concerns will be carefully reviewed and evaluated by our technical team, and appropriate corrective actions will be taken after thorough assessment.</p>
                                
                                <p>Thank you for your cooperation and understanding.</p>';
                    } else { // recommendation
                        $mail->Body .= '
                                <p>We highly appreciate your recommendations and valuable inputs. Rest assured that these will be carefully evaluated by our team and will be taken into consideration after thorough review and assessment.</p>
                                
                                <p>Thank you for your continued support and cooperation.</p>';
                    }

                    $mail->Body .= '
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
            \Log::error('PHPMailer Error: ' . $e->getMessage());
            return false;
        }
    }
}