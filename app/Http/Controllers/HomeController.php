<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patients;
use App\Models\Facility;
use App\Models\Province;
use App\Models\Muncity;
use App\Models\Barangay;
use App\Models\Fundsource;
use App\Models\Proponent;
use App\Models\Group;
use App\Models\ProponentInfo;
use App\Models\User;
use App\Models\Transfer;
use App\Models\Dv;
use App\Models\Dv2;
use App\Models\OnlineUser;
use App\Models\Dv3;
use App\Models\NewDV;
use App\Models\Utilization;
use App\Models\TrackingDetails;
use App\Models\AddFacilityInfo;
use App\Models\PatientLogs;
use App\Models\MailHistory;
use App\Models\ReturnedPatients;
use App\Models\ProponentUtilizationV1;
use App\Models\IncludedFacility;
use App\Models\Logbook;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use DataTables;
use Kyslik\ColumnSortable\Sortable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function getNames(Request $request, $type)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = 50;
        
        if($type == 1){
            $query = Patients::select('fname')
                ->distinct()
                ->orderBy('fname');
        }else if($type == 2){
            $query = Patients::select('fname')
                ->where(function ($query) {
                    $query->whereNull('pro_used')
                        ->where('fc_status', "returned");
                })
                ->distinct()
                ->orderBy('fname');
        }else if($type == 3){
            $query = Patients::select('fname')
                ->where(function ($query) {
                    $query->whereNull('pro_used')
                        ->where('expired', 1);
                })
                ->distinct()
                ->orderBy('fname');
        }else{
            $query = Patients::select('fname')
                ->whereRaw("
                    NOT EXISTS (
                        SELECT 1 FROM dohdtr.users 
                        WHERE dohdtr.users.userid = patients.created_by
                    )
                ")
                ->distinct()
                ->orderBy('fname');
        }
        
        if (!empty($search)) {
            $query->where('fname', 'LIKE', '%' . $search . '%');
        }
        
        $totalCount = $query->count();
        $results = $query->skip(($page - 1) * $perPage)
                        ->take($perPage)
                        ->get();
        
        return response()->json([
            'results' => $results->map(fn($item) => [
                'id' => $item->fname,
                'text' => $item->fname
            ]),
            'has_more' => ($page * $perPage) < $totalCount
        ]);
    }

    public function getDates(Request $request, $type)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = 50;

        if($type == 1){
            $query = Patients::select('date_guarantee_letter')
                ->distinct()
                ->orderBy('date_guarantee_letter');
        }else if($type == 2){
            $query = Patients::select('date_guarantee_letter')
                ->where(function ($query) {
                    $query->whereNull('pro_used')
                        ->where('fc_status', "returned");
                })
                ->distinct()
                ->orderBy('date_guarantee_letter');
        }else if($type == 3){
            $query = Patients::select('date_guarantee_letter')
                ->where(function ($query) {
                    $query->whereNull('pro_used')
                        ->where('expired', 1);
                })
                ->distinct()
                ->orderBy('date_guarantee_letter');
        }else{
            $query = Patients::select('date_guarantee_letter')
                ->whereRaw("
                    NOT EXISTS (
                        SELECT 1 FROM dohdtr.users 
                        WHERE dohdtr.users.userid = patients.created_by
                    )
                ")
                ->distinct()
                ->orderBy('date_guarantee_letter');
        }
        
        if (!empty($search)) {
            $query->where(DB::raw("DATE_FORMAT(date_guarantee_letter, '%M %e, %Y')"), 'LIKE', '%' . $search . '%');
        }
        
        $totalCount = $query->count();
        $results = $query->skip(($page - 1) * $perPage)
                        ->take($perPage)
                        ->get();
        
        return response()->json([
            'results' => $results->map(fn($item) => [
                'id' => $item->date_guarantee_letter,
                'text' => date('F j, Y', strtotime($item->date_guarantee_letter))
            ]),
            'has_more' => ($page * $perPage) < $totalCount
        ]);
    }

    public function getMNames(Request $request, $type)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = 50;

        if($type == 1){
            $query = Patients::select('mname')
                ->distinct()
                ->orderBy('mname');
        }else if($type == 2){
            $query = Patients::select('mname')
                ->where(function ($query) {
                    $query->whereNull('pro_used')
                        ->where('fc_status', "returned");
                })
                ->distinct()
                ->orderBy('mname');
        }else if($type == 3){
            $query = Patients::select('mname')
                ->where(function ($query) {
                    $query->whereNull('pro_used')
                        ->where('expired', 1);
                })
                ->distinct()
                ->orderBy('mname');
        }else{
            $query = Patients::select('mname')
                ->whereRaw("
                    NOT EXISTS (
                        SELECT 1 FROM dohdtr.users 
                        WHERE dohdtr.users.userid = patients.created_by
                    )
                ")
                ->distinct()
                ->orderBy('mname');
        }
        
        if (!empty($search)) {
            $query->where('mname', 'LIKE', '%' . $search . '%');
        }
        
        $totalCount = $query->count();
        $results = $query->skip(($page - 1) * $perPage)
                        ->take($perPage)
                        ->get();
        
        return response()->json([
            'results' => $results->map(fn($item) => [
                'id' => $item->mname,
                'text' => $item->mname
            ]),
            'has_more' => ($page * $perPage) < $totalCount
        ]);
    }

    public function getLNames(Request $request, $type)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = 50;

        if($type == 1){
            $query = Patients::select('lname')
                ->distinct()
                ->orderBy('lname');
        }else if($type == 2){
            $query = Patients::select('lname')
                ->where(function ($query) {
                    $query->whereNull('pro_used')
                        ->where('fc_status', "returned");
                })
                ->distinct()
                ->orderBy('lname');
        }else if($type == 3){
            $query = Patients::select('lname')
                ->where(function ($query) {
                    $query->whereNull('pro_used')
                        ->where('expired', 1);
                })
                ->distinct()
                ->orderBy('lname');
        }else{
            $query = Patients::select('lname')
                ->whereRaw("
                    NOT EXISTS (
                        SELECT 1 FROM dohdtr.users 
                        WHERE dohdtr.users.userid = patients.created_by
                    )
                ")
                ->distinct()
                ->orderBy('lname');
        }
        
        if (!empty($search)) {
            $query->where('lname', 'LIKE', '%' . $search . '%');
        }
        
        $totalCount = $query->count();
        $results = $query->skip(($page - 1) * $perPage)
                        ->take($perPage)
                        ->get();
        
        return response()->json([
            'results' => $results->map(fn($item) => [
                'id' => $item->lname,
                'text' => $item->lname
            ]),
            'has_more' => ($page * $perPage) < $totalCount
        ]);
    }

    public function getFacilities(Request $request)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = 50;
        
        $query = Facility::select('name','id')
                         ->distinct()
                         ->orderBy('name');
        
        if (!empty($search)) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }
        
        $totalCount = $query->count();

        $results = $query->skip(($page - 1) * $perPage)
                        ->take($perPage)
                        ->get();
        
        return response()->json([
            'results' => $results->map(fn($item) => [
                'id' => $item->id,
                'text' => $item->name
            ]),
            'has_more' => ($page * $perPage) < $totalCount
        ]);
    }

    public function getProponents(Request $request)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = 50;
        
        $query = Proponent::selectRaw('MAX(id) as id, proponent')
                    ->groupBy('proponent')
                    ->orderBy('proponent');
        if (!empty($search)) {
            $query->where('proponent', 'LIKE', '%' . $search . '%');
        }
        
        $totalCount = count($query->get());

        $results = $query->skip(($page - 1) * $perPage)
                        ->take($perPage)
                        ->get();
        
        return response()->json([
            'results' => $results->map(fn($item) => [
                'id' => $item->id,
                'text' => $item->proponent
            ]),
            'has_more' => ($page * $perPage) < $totalCount
        ]);
    }

    public function getRegion(Request $request, $type)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = 50;

        if($type == 1){
            $query = Patients::select('region')
                         ->distinct()
                         ->orderBy('region');
        }else if($type == 2){
            $query = Patients::select('region')
                ->where(function ($query) {
                    $query->whereNull('pro_used')
                        ->where('fc_status', "returned");
                })
                ->distinct()
                ->orderBy('region');
        }else if($type == 3){
            $query = Patients::select('region')
                ->where(function ($query) {
                    $query->whereNull('pro_used')
                        ->where('expired', 1);
                })
                ->distinct()
                ->orderBy('region');
        }else{
            $query = Patients::select('region')
                ->whereRaw("
                    NOT EXISTS (
                        SELECT 1 FROM dohdtr.users 
                        WHERE dohdtr.users.userid = patients.created_by
                    )
                ")
                ->distinct()
                ->orderBy('region');
        }
                         
        if (!empty($search)) {
            $query->where('region', 'LIKE', '%' . $search . '%');
        }
        
        $totalCount = count($query->get());
        $results = $query->skip(($page - 1) * $perPage)
                        ->take($perPage)
                        ->get();
        
        return response()->json([
            'results' => $results->map(fn($item) => [
                'id' => $item->region,
                'text' => $item->region
            ]),
            'has_more' => ($page * $perPage) < $totalCount
        ]);
    }

    public function getProvinces(Request $request, $type)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = 50;
        
        if($type == 1){
            $query1 = DB::table('patients')
                ->select(DB::raw("DISTINCT other_province AS province"))
                ->whereNotNull('other_province');
        }else if($type == 2){
            $query1 = DB::table('patients')
                ->select(DB::raw("DISTINCT other_province AS province"))
                ->where(function ($query) {
                    $query->whereNull('pro_used')
                        ->where('fc_status', "returned");
                })
                ->whereNotNull('other_province');
        }else if($type == 3){
            $query1 = DB::table('patients')
                ->select(DB::raw("DISTINCT other_province AS province"))
                ->where(function ($query) {
                    $query->whereNull('pro_used')
                        ->where('expired', 1);
                })
                ->whereNotNull('other_province');
        }else{
            $query1 = DB::table('patients')
                ->select(DB::raw("DISTINCT other_province AS province"))
                ->whereNotNull('other_province')
                ->whereRaw("
                    NOT EXISTS (
                        SELECT 1 FROM dohdtr.users 
                        WHERE dohdtr.users.userid = patients.created_by
                    )
                ");
        }

        $query2 = DB::table('province')
            ->select(DB::raw("DISTINCT description AS province"))
            ->whereNotNull('description');

        if (!empty($search)) {
            $query1->where('other_province', 'LIKE', '%' . $search . '%');
            $query2->where('description', 'LIKE', '%' . $search . '%');
        }

        $merged = $query1->union($query2);

        $result = DB::table(DB::raw("({$merged->toSql()}) as merged"))
            ->mergeBindings($merged) 
            ->orderBy('province');

        $totalCount = count($result->get());

        $results = $result->skip(($page - 1) * $perPage)
                        ->take($perPage)
                        ->get();
        
        return response()->json([
            'results' => $results->map(fn($item) => [
                'id' => $item->province,
                'text' => $item->province
            ]),
            'has_more' => ($page * $perPage) < $totalCount
        ]);
    }

    public function getMunicipalities(Request $request, $type)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = 50;

        if($type == 1){
            $query1 = DB::table('patients')
                ->select(DB::raw("DISTINCT other_muncity AS municipality"))
                ->whereNotNull('other_muncity');
        }else if($type == 2){
            $query1 = DB::table('patients')
            ->select(DB::raw("DISTINCT other_muncity AS municipality"))
            ->where(function ($query) {
                    $query->whereNull('pro_used')
                        ->where('fc_status', "returned");
                })
                ->whereNotNull('other_muncity');
        }else if($type == 3){
            $query1 = DB::table('patients')
            ->select(DB::raw("DISTINCT other_muncity AS municipality"))
            ->where(function ($query) {
                    $query->whereNull('pro_used')
                        ->where('expired', 1);
                })
                ->whereNotNull('other_muncity');
        }else{
            $query1 = DB::table('patients')->select(DB::raw("DISTINCT other_muncity AS municipality"))
                ->whereNotNull('other_muncity')
                ->whereRaw("
                    NOT EXISTS (
                        SELECT 1 FROM dohdtr.users 
                        WHERE dohdtr.users.userid = patients.created_by
                    )
                ");
        }

        $query2 = DB::table('muncity')
            ->select(DB::raw("DISTINCT description AS municipality"))
            ->whereNotNull('description');

        if (!empty($search)) {
            $query1->where('other_muncity', 'LIKE', '%' . $search . '%');
            $query2->where('description', 'LIKE', '%' . $search . '%');
        }

        $merged = $query1->union($query2);

        $result = DB::table(DB::raw("({$merged->toSql()}) as merged"))
            ->mergeBindings($merged) 
            ->orderBy('municipality');

        $totalCount = count($result->get());

        $results = $result->skip(($page - 1) * $perPage)
                        ->take($perPage)
                        ->get();
        
        return response()->json([
            'results' => $results->map(fn($item) => [
                'id' => $item->municipality,
                'text' => $item->municipality
            ]),
            'has_more' => ($page * $perPage) < $totalCount
        ]);
    }

    public function getBarangay(Request $request, $type)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = 50;

        if($type == 1){
            $query1 = DB::table('patients')
                ->select(DB::raw("DISTINCT other_barangay AS barangay"))
                ->whereNotNull('other_barangay');
        }else if($type == 2){
            $query1 = DB::table('patients')
            ->select(DB::raw("DISTINCT other_barangay AS barangay"))
            ->where(function ($query) {
                    $query->whereNull('pro_used')
                        ->where('fc_status', "returned");
                })
            ->whereNotNull('other_barangay');
        }else if($type == 3){
            $query1 = DB::table('patients')
            ->select(DB::raw("DISTINCT other_barangay AS barangay"))
            ->where(function ($query) {
                    $query->whereNull('pro_used')
                        ->where('expired', 1);
                })
            ->whereNotNull('other_barangay');
        }else{
            $query1 = DB::table('patients')
                ->select(DB::raw("DISTINCT other_barangay AS barangay"))
                ->whereNotNull('other_barangay')
                ->whereRaw("
                    NOT EXISTS (
                        SELECT 1 FROM dohdtr.users 
                        WHERE dohdtr.users.userid = patients.created_by
                    )
                ");
        }

        $query2 = DB::table('barangay')
            ->select(DB::raw("DISTINCT description AS barangay"))
            ->whereNotNull('description');

        if (!empty($search)) {
            $query1->where('other_barangay', 'LIKE', '%' . $search . '%');
            $query2->where('description', 'LIKE', '%' . $search . '%');
        }

        $merged = $query1->union($query2);

        $result = DB::table(DB::raw("({$merged->toSql()}) as merged"))
            ->mergeBindings($merged) 
            ->orderBy('barangay');

        $totalCount = count($result->get());

        $results = $result->skip(($page - 1) * $perPage)
                        ->take($perPage)
                        ->get();
        
        return response()->json([
            'results' => $results->map(fn($item) => [
                'id' => $item->barangay,
                'text' => $item->barangay
            ]),
            'has_more' => ($page * $perPage) < $totalCount
        ]);
    }

    public function getCreatedAt(Request $request, $type)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = 50;
        
        if($type == 1){
            $query = Patients::select(DB::raw('DATE(created_at) as created_date'))
                ->distinct()->orderBy('created_date');
        }else if($type == 2){
            $query = Patients::select(DB::raw('DATE(created_at) as created_date'))
                ->where(function ($query) {
                    $query->whereNull('pro_used')
                        ->where('fc_status', "returned");
                })
                ->distinct()->orderBy('created_date');
        }else if($type == 3){
            $query = Patients::select(DB::raw('DATE(created_at) as created_date'))
                ->where(function ($query) {
                    $query->whereNull('pro_used')
                        ->where('expired', 1);
                })
                ->distinct()->orderBy('created_date');
        }else{
            $query = Patients::select(DB::raw('DATE(created_at) as created_date'))
            ->whereRaw("
                NOT EXISTS (
                    SELECT 1 FROM dohdtr.users 
                    WHERE dohdtr.users.userid = patients.created_by
                )
            ")->distinct()->orderBy('created_date');
        }

        if (!empty($search)) {
            $query->where(DB::raw("DATE_FORMAT(created_at, '%M %e, %Y')"), 'LIKE', '%' . $search . '%');
        }
        
        $totalCount = $query->count();
        $results = $query->skip(($page - 1) * $perPage)
                        ->take($perPage)
                        ->get();
        
        return response()->json([
            'results' => $results->map(fn($item) => [
                'id' => $item->created_date,
                'text' => date('F j, Y', strtotime($item->created_date))
            ]),
            'has_more' => ($page * $perPage) < $totalCount
        ]);
    }

    public function getCreatedBy(Request $request, $type)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = 50;
        
        if($type == 1){
            $query = Patients::select('created_by')
                        ->distinct()->pluck('created_by')->toArray();
        }else if($type == 2){
            $query = Patients::select('created_by')
                ->where(function ($query) {
                    $query->whereNull('pro_used')
                        ->where('fc_status', "returned");
                })
                ->distinct()->pluck('created_by')->toArray();
        }else if($type == 3){
            $query = Patients::select('created_by')
                ->where(function ($query) {
                    $query->whereNull('pro_used')
                        ->where('expired', 1);
                })
                ->distinct()->pluck('created_by')->toArray();
        }else{
            $query = Patients::select('created_by')
            ->whereRaw("
                NOT EXISTS (
                    SELECT 1 FROM dohdtr.users 
                    WHERE dohdtr.users.userid = patients.created_by
                )
            ")->distinct()->pluck('created_by')->toArray();
        }

        $query = User::whereIn('userid', $query)->select('userid', 'lname', 'fname');

        if (!empty($search)) {
            $query->where('lname', 'LIKE', '%' . $search . '%')->orWhere('fname', 'LIKE', '%' . $search . '%');
        }
        
        $totalCount = $query->count();
        $results = $query->skip(($page - 1) * $perPage)
                        ->take($perPage)
                        ->get();
        
        return response()->json([
            'results' => $results->map(fn($item) => [
                'id' => $item->userid,
                'text' => $item->fname.' '.$item->lname
            ]),
            'has_more' => ($page * $perPage) < $totalCount
        ]);
    }

    public function index(Request $request)
    {
        Proponent::whereIn('proponent', 
            Proponent::where('status', 1)->pluck('proponent')
        )->update(['status' => 1]);

        $filter_date = $request->input('filter_dates');
        $order = $request->input('order');

        $baseQuery = Patients::with([
            'province:id,description',
            'muncity:id,description', 
            'barangay:id,description',
            'encoded_by:userid,fname,lname,mname',
            'gl_user:username,fname,lname',
            'facility:id,name',
            'proponentData:id,proponent',
            'pat_remarks:patient_id,remarks'
        ])->where(function ($query) {
            $query->whereNull('pro_used')
                  ->where(function ($q) {
                      $q->whereNull('fc_status')
                        ->orWhere('fc_status', '!=', 'returned');
                  })
                  ->where(function ($q) {
                      $q->whereNull('expired')
                        ->orWhere('expired', '!=', 1);
                  });
        });

        if ($request->gen && $filter_date) {
            $dateRange = explode(' - ', $filter_date);
            $start_date = date('Y-m-d', strtotime($dateRange[0]));
            $end_date = date('Y-m-d', strtotime($dateRange[1]));
            $baseQuery->whereBetween('created_at', [$start_date, $end_date . ' 23:59:59']);
        }

        if ($request->viewAll) {
            $request->merge([
                'keyword' => '',
                'filter_date' => '',
                'filter_fname' => '',
                'filter_mname' => '',
                'filter_lname' => '',
                'filter_facility' => '',
                'filter_proponent' => '',
                'filter_code' => '',
                'filter_region' => '',
                'filter_province' => '',
                'filter_muncity' => '',
                'filter_barangay' => '',
                'filter_on' => '',
                'filter_by' => '',
                'gen' => ''
            ]);
            $filter_date = '';
        } elseif ($request->keyword) {
            $keyword = $request->keyword;
            $baseQuery->where(function ($query) use ($keyword) {
                $query->where('fname', 'LIKE', "%$keyword%")
                    ->orWhere('lname', 'LIKE', "%$keyword%")
                    ->orWhere('mname', 'LIKE', "%$keyword%")
                    ->orWhere('region', 'LIKE', "%$keyword%")
                    ->orWhere('other_province', 'LIKE', "%$keyword%")
                    ->orWhere('other_muncity', 'LIKE', "%$keyword%")
                    ->orWhere('other_barangay', 'LIKE', "%$keyword%")
                    ->orWhere('patient_code', 'LIKE', "%$keyword%")
                    ->orWhereHas('facility', function ($q) use ($keyword) {
                        $q->where('name', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('proponentData', function ($q) use ($keyword) {
                        $q->where('proponent', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('barangay', function ($q) use ($keyword) {
                        $q->where('description', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('muncity', function ($q) use ($keyword) {
                        $q->where('description', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('province', function ($q) use ($keyword) {
                        $q->where('description', 'LIKE', "%$keyword%");
                    });
            });
        }

        $this->applyColumnFilters($baseQuery, $request);

        $this->applySorting($baseQuery, $request);

        $patients = $baseQuery->orderBy('updated_at', 'desc')->paginate(50);
        $filter_type = 1;
        $filterData = $this->getFilterData($request, $filter_type);

        return view('home', array_merge([
            'patients' => $patients,
            'keyword' => $request->keyword,
            'user' => Auth::user(),
            'generate_dates' => $filter_date,
            'gen' => $request->gen,
            'order' => $order,
            'id_pat' => '',
            'active_facility' => OnlineUser::where('user_type', 2)->pluck('type_identity')->toArray()
        ], $filterData));
    }

    private function applyColumnFilters($query, $request)
        {
        $filters = [
            'filter_date' => 'date_guarantee_letter',
            'filter_fname' => 'fname',
            'filter_mname' => 'mname',
            'filter_lname' => 'lname',
            'filter_facility' => 'facility_id',
            'filter_proponent' => 'proponent_id',
            'filter_code' => 'patient_code',
            'filter_region' => 'region',
            'filter_by' => 'created_by'
        ];

        foreach ($filters as $requestKey => $column) {
            if ($request->$requestKey) {
                $query->whereIn($column, explode(',', $request->$requestKey));
            }
        }

        if ($request->filter_province) {
            $values = explode(',', $request->filter_province);
            $query->where(function ($q) use ($values) {
                $q->whereIn('province_id', $values)
                ->orWhereIn('other_province', $values);
            });
        }

        if ($request->filter_municipality) {
            $values = explode(',', $request->filter_municipality);
            $query->where(function ($q) use ($values) {
                $q->whereIn('muncity_id', $values)
                ->orWhereIn('other_muncity', $values);
            });
        }

        if ($request->filter_barangay) {
            $values = explode(',', $request->filter_barangay);
            $query->where(function ($q) use ($values) {
                $q->whereIn('barangay_id', $values)
                ->orWhereIn('other_barangay', $values);
            });
        }

        if ($request->filter_on) {
            $query->whereIn(DB::raw('DATE(created_at)'), explode(',', $request->filter_on));
        }
    }

    private function applySorting($query, $request)
    {
        if (!$request->sort) {
            $query->orderBy('id', 'desc');
            return;
        }

        $order = $request->input('order');

        switch ($request->sort) {
            case 'facility':
                $query->sortable(['facility.name' => $order]);
                break;
            case 'proponent':
                $query->sortable(['proponentData.proponent' => $order]);
                break;
            case 'province':
                $query->leftJoin('province', 'province.id', '=', 'patients.province_id')
                    ->orderBy('patients.other_province', $order)
                    ->orderBy('province.description', $order)
                    ->select('patients.*');
                break;
            case 'municipality':
                $query->leftJoin('muncity', 'muncity.id', '=', 'patients.muncity_id')
                    ->orderBy('patients.other_muncity', $order)
                    ->orderBy('muncity.description', $order)
                    ->select('patients.*');
                break;
            case 'barangay':
                $query->leftJoin('barangay', 'barangay.id', '=', 'patients.barangay_id')
                    ->orderBy('patients.other_barangay', $order)
                    ->orderBy('barangay.description', $order)
                    ->select('patients.*');
                break;
            case 'encoded_by':
                $query->orderBy(
                    \DB::connection('dohdtr')
                        ->table('users')
                        ->select('lname')
                        ->whereColumn('users.userid', 'patients.created_by'),
                    $order
                );
                break;
            default:
                $query->sortable(['id' => 'desc']);
        }
    }

    private function getFilterData($request, $filter_type)
    {
        if($filter_type == 1){
            $basePatients = Patients::whereNull('pro_used');
        }else{
            $basePatients = Patients::whereRaw("
                NOT EXISTS (
                    SELECT 1 FROM dohdtr.users 
                    WHERE dohdtr.users.userid = patients.created_by
                )
            ");
        }

        $facilityIds = $basePatients->distinct()->pluck('facility_id')->filter();
        $proponentIds = $basePatients->distinct()->pluck('proponent_id')->filter();
        $userIds = $basePatients->distinct()->pluck('created_by')->filter();
        $barangayIds = $basePatients->distinct()->pluck('barangay_id')->filter();
        $muncityIds = $basePatients->distinct()->pluck('muncity_id')->filter();
        $provinceIds = $basePatients->distinct()->pluck('province_id')->filter();

        $includedIds = cache()->remember('included_facility_ids', 3600, function () {
            return IncludedFacility::pluck('facility_id')->toArray();
        });

        $proponentsCode = cache()->remember('proponents_code', 3600, function () {
            return Proponent::groupBy('proponent_code')
                ->select(DB::raw('MAX(proponent) as proponent'), 
                        DB::raw('MAX(proponent_code) as proponent_code'),
                        DB::raw('MAX(id) as id'))
                ->get();
        });

        return [
            'provinces' => Province::select('id', 'description')->get(),
            'municipalities' => Muncity::select('id', 'description')->get(),
            'proponents' => $proponentsCode,
            'barangays' => Barangay::select('id', 'description')->get(),
            'facilities' => Facility::whereIn('id', $includedIds)->get(),
            'filter_date' => explode(',', $request->filter_date ?? ''),
            'filter_fname' => explode(',', $request->filter_fname ?? ''),
            'filter_mname' => explode(',', $request->filter_mname ?? ''),
            'filter_lname' => explode(',', $request->filter_lname ?? ''),
            'filter_facility' => explode(',', $request->filter_facility ?? ''),
            'filter_proponent' => explode(',', $request->filter_proponent ?? ''),
            'filter_code' => explode(',', $request->filter_code ?? ''),
            'filter_region' => explode(',', $request->filter_region ?? ''),
            'filter_province' => explode(',', $request->filter_province ?? ''),
            'filter_municipality' => explode(',', $request->filter_municipality ?? ''),
            'filter_barangay' => explode(',', $request->filter_barangay ?? ''),
            'filter_on' => explode(',', $request->filter_on ?? ''),
            'filter_by' => explode(',', $request->filter_by ?? ''),
            
            'onhold_facs' => AddFacilityInfo::where('sent_status', 1)->pluck('facility_id')->toArray()
        ];
    }

    public function patients(Request $request)
    {
        $filter_date = $request->input('filter_dates');
        $order = $request->input('order');

        $baseQuery = Patients::with([
            'province:id,description',
            'muncity:id,description', 
            'barangay:id,description',
            'encoded_by:userid,fname,lname,mname',
            'gl_user:username,fname,lname',
            'facility:id,name',
            'proponentData:id,proponent',
            'pat_remarks:patient_id,remarks'
        ])->whereRaw("
            NOT EXISTS (
                SELECT 1 FROM dohdtr.users 
                WHERE dohdtr.users.userid = patients.created_by
            )
        ")->whereNotNull('sent_type');

        if ($request->gen && $filter_date) {
            $dateRange = explode(' - ', $filter_date);
            $start_date = date('Y-m-d', strtotime($dateRange[0]));
            $end_date = date('Y-m-d', strtotime($dateRange[1]));
            $baseQuery->whereBetween('created_at', [$start_date, $end_date . ' 23:59:59']);
        }

        if ($request->viewAll) {
            $request->merge([
                'keyword' => '',
                'filter_date' => '',
                'filter_fname' => '',
                'filter_mname' => '',
                'filter_lname' => '',
                'filter_facility' => '',
                'filter_proponent' => '',
                'filter_code' => '',
                'filter_region' => '',
                'filter_province' => '',
                'filter_muncity' => '',
                'filter_barangay' => '',
                'filter_on' => '',
                'filter_by' => '',
                'gen' => ''
            ]);
            $filter_date = '';
        } elseif ($request->keyword) {
            $keyword = $request->keyword;
            $baseQuery->where(function ($query) use ($keyword) {
                $query->where('fname', 'LIKE', "%$keyword%")
                    ->orWhere('lname', 'LIKE', "%$keyword%")
                    ->orWhere('mname', 'LIKE', "%$keyword%")
                    ->orWhere('region', 'LIKE', "%$keyword%")
                    ->orWhere('other_province', 'LIKE', "%$keyword%")
                    ->orWhere('other_muncity', 'LIKE', "%$keyword%")
                    ->orWhere('other_barangay', 'LIKE', "%$keyword%")
                    ->orWhere('patient_code', 'LIKE', "%$keyword%")
                    ->orWhereHas('facility', function ($q) use ($keyword) {
                        $q->where('name', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('proponentData', function ($q) use ($keyword) {
                        $q->where('proponent', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('barangay', function ($q) use ($keyword) {
                        $q->where('description', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('muncity', function ($q) use ($keyword) {
                        $q->where('description', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('province', function ($q) use ($keyword) {
                        $q->where('description', 'LIKE', "%$keyword%");
                    });
            });
        }

        $this->applyColumnFilters($baseQuery, $request);

        $this->applySorting($baseQuery, $request);

        $patients = $baseQuery->orderBy('updated_at', 'desc')->paginate(50);
        $filter_type = 2;
        $filterData = $this->getFilterData($request, $filter_type);

        return view('maif.proponent_patient', array_merge([
            'patients' => $patients,
            'keyword' => $request->keyword,
            'user' => Auth::user(),
            'generate_dates' => $filter_date,
            'gen' => $request->gen,
            'order' => $order,
            'id_pat' => '',
        ], $filterData));
    }

    public function returnedPatients(Request $request)
    {
        Proponent::whereIn('proponent', 
            Proponent::where('status', 1)->pluck('proponent')
        )->update(['status' => 1]);

        $filter_date = $request->input('filter_dates');
        $order = $request->input('order');

        $baseQuery = Patients::with([
            'province:id,description',
            'muncity:id,description', 
            'barangay:id,description',
            'encoded_by:userid,fname,lname,mname',
            'gl_user:username,fname,lname',
            'facility:id,name',
            'proponentData:id,proponent',
            'pat_remarks:patient_id,remarks'
        ])->where(function ($query) {
            $query->whereNull('pro_used')
                ->where('fc_status', "returned");
        });

        if ($request->gen && $filter_date) {
            $dateRange = explode(' - ', $filter_date);
            $start_date = date('Y-m-d', strtotime($dateRange[0]));
            $end_date = date('Y-m-d', strtotime($dateRange[1]));
            $baseQuery->whereBetween('created_at', [$start_date, $end_date . ' 23:59:59']);
        }

        if ($request->viewAll) {
            $request->merge([
                'keyword' => '',
                'filter_date' => '',
                'filter_fname' => '',
                'filter_mname' => '',
                'filter_lname' => '',
                'filter_facility' => '',
                'filter_proponent' => '',
                'filter_code' => '',
                'filter_region' => '',
                'filter_province' => '',
                'filter_muncity' => '',
                'filter_barangay' => '',
                'filter_on' => '',
                'filter_by' => '',
                'gen' => ''
            ]);
            $filter_date = '';
        } elseif ($request->keyword) {
            $keyword = $request->keyword;
            $baseQuery->where(function ($query) use ($keyword) {
                $query->where('fname', 'LIKE', "%$keyword%")
                    ->orWhere('lname', 'LIKE', "%$keyword%")
                    ->orWhere('mname', 'LIKE', "%$keyword%")
                    ->orWhere('region', 'LIKE', "%$keyword%")
                    ->orWhere('other_province', 'LIKE', "%$keyword%")
                    ->orWhere('other_muncity', 'LIKE', "%$keyword%")
                    ->orWhere('other_barangay', 'LIKE', "%$keyword%")
                    ->orWhere('patient_code', 'LIKE', "%$keyword%")
                    ->orWhereHas('facility', function ($q) use ($keyword) {
                        $q->where('name', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('proponentData', function ($q) use ($keyword) {
                        $q->where('proponent', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('barangay', function ($q) use ($keyword) {
                        $q->where('description', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('muncity', function ($q) use ($keyword) {
                        $q->where('description', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('province', function ($q) use ($keyword) {
                        $q->where('description', 'LIKE', "%$keyword%");
                    });
            });
        }

        $this->applyColumnFilters($baseQuery, $request);

        $this->applySorting($baseQuery, $request);

        $patients = $baseQuery->orderBy('updated_at', 'desc')->paginate(50);
        $filter_type = 1;
        $filterData = $this->getFilterData($request, $filter_type);

        return view('maif.returned_patients', array_merge([
            'patients' => $patients,
            'keyword' => $request->keyword,
            'user' => Auth::user(),
            'generate_dates' => $filter_date,
            'gen' => $request->gen,
            'order' => $order,
            'id_pat' => '',
            'active_facility' => OnlineUser::where('user_type', 2)->pluck('type_identity')->toArray()
        ], $filterData));
    }

    public function expiredPatients(Request $request)
    {
        Proponent::whereIn('proponent', 
            Proponent::where('status', 1)->pluck('proponent')
        )->update(['status' => 1]);

        $filter_date = $request->input('filter_dates');
        $order = $request->input('order');

        $baseQuery = Patients::with([
            'province:id,description',
            'muncity:id,description', 
            'barangay:id,description',
            'encoded_by:userid,fname,lname,mname',
            'gl_user:username,fname,lname',
            'facility:id,name',
            'proponentData:id,proponent',
            'pat_remarks:patient_id,remarks'
        ])->where(function ($query) {
            $query->whereNull('pro_used')
                ->where('expired', 1);
        });

        if ($request->gen && $filter_date) {
            $dateRange = explode(' - ', $filter_date);
            $start_date = date('Y-m-d', strtotime($dateRange[0]));
            $end_date = date('Y-m-d', strtotime($dateRange[1]));
            $baseQuery->whereBetween('created_at', [$start_date, $end_date . ' 23:59:59']);
        }

        if ($request->viewAll) {
            $request->merge([
                'keyword' => '',
                'filter_date' => '',
                'filter_fname' => '',
                'filter_mname' => '',
                'filter_lname' => '',
                'filter_facility' => '',
                'filter_proponent' => '',
                'filter_code' => '',
                'filter_region' => '',
                'filter_province' => '',
                'filter_muncity' => '',
                'filter_barangay' => '',
                'filter_on' => '',
                'filter_by' => '',
                'gen' => ''
            ]);
            $filter_date = '';
        } elseif ($request->keyword) {
            $keyword = $request->keyword;
            $baseQuery->where(function ($query) use ($keyword) {
                $query->where('fname', 'LIKE', "%$keyword%")
                    ->orWhere('lname', 'LIKE', "%$keyword%")
                    ->orWhere('mname', 'LIKE', "%$keyword%")
                    ->orWhere('region', 'LIKE', "%$keyword%")
                    ->orWhere('other_province', 'LIKE', "%$keyword%")
                    ->orWhere('other_muncity', 'LIKE', "%$keyword%")
                    ->orWhere('other_barangay', 'LIKE', "%$keyword%")
                    ->orWhere('patient_code', 'LIKE', "%$keyword%")
                    ->orWhereHas('facility', function ($q) use ($keyword) {
                        $q->where('name', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('proponentData', function ($q) use ($keyword) {
                        $q->where('proponent', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('barangay', function ($q) use ($keyword) {
                        $q->where('description', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('muncity', function ($q) use ($keyword) {
                        $q->where('description', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('province', function ($q) use ($keyword) {
                        $q->where('description', 'LIKE', "%$keyword%");
                    });
            });
        }

        $this->applyColumnFilters($baseQuery, $request);

        $this->applySorting($baseQuery, $request);

        $patients = $baseQuery->orderBy('updated_at', 'desc')->paginate(50);
        $filter_type = 1;
        $filterData = $this->getFilterData($request, $filter_type);

        return view('maif.expired_patients', array_merge([
            'patients' => $patients,
            'keyword' => $request->keyword,
            'user' => Auth::user(),
            'generate_dates' => $filter_date,
            'gen' => $request->gen,
            'order' => $order,
            'id_pat' => '',
            'active_facility' => OnlineUser::where('user_type', 2)->pluck('type_identity')->toArray()
        ], $filterData));
    }

    public function fetchAdditionalData(){
        return [
            'all_pat' => Patients::get(),
            'proponents' => Proponent::get()
        ];
    }

    public function proponentPatient(){
        return [
            'all_pat' => Patients::whereNotNull('sent_type')->select('id')->get(),
            'proponents' => Proponent::get()
        ];
    }

    public function updateGl($id){

        $patients = Patients::where('id', $id)->with([
            'facility:id,name','province:id,description',
            'muncity:id,description',
            'barangay:id,description',
            'proponentData:id,proponent'
            ])->first();
        $pro = Proponent::where('proponent', Proponent::where('id', $patients->proponent_id)->value('proponent'))->pluck('id')->toArray();
        return $data=[
            'ids' => $pro,
            'patients' => $patients
        ];
    }

    public function report(Request $request){

        $proponents = Proponent::groupBy('pro_group')->select(DB::raw('MAX(proponent) as proponent'), DB::raw('MAX(pro_group) as pro_group'),DB::raw('MAX(id) as id') );
        
        if($request->viewAll){
            $request->keyword = '';
        }else if($request->keyword){
            $proponents->where('proponent', 'LIKE', "%$request->keyword%");
        }
        $proponents = $proponents->orderBy('id', 'desc')->paginate(15);

        return view('report', ['proponents'=> $proponents, 'keyword'=>$request->keyword]);
    }

    public function reportFacility(Request $request){
        // $facilities = ProponentInfo::groupBy('facility_id')
        //     ->select(DB::raw('MAX(facility_id) as facility_id'))
        //     ->with(['facility' => function ($query) use ($request) {
        //         if (!$request->viewAll && $request->keyword) {
        //             $query->where('name', 'LIKE', "%$request->keyword%");
        //         }
        //     }])
        //     ->paginate(15);
        $facilities = Facility::when(!$request->viewAll && $request->keyword, function ($query) use ($request) {
            $query->where('name', 'LIKE', "%$request->keyword%");
        })
        ->paginate(15);

        if($request->viewAll){
            $request->keyword = '';
        }

        return view('report.facility_report', ['facilities'=>$facilities, 'keyword' => $request->keyword]);
    }

    public function getProponentReport($pro_group){
        $proponentIds = Proponent::where('pro_group', $pro_group)->pluck('id')->toArray();
        $utilization = Utilization::whereIn('proponent_id', $proponentIds)
            ->select( DB::raw('MAX(utilization.div_id) as route_no'), DB::raw('MAX(utilization.utilize_amount) as utilize_amount'),  
                DB::raw('MAX(proponent.proponent) as proponent_name'), DB::raw('MAX(utilization.created_at) as created_at'),
                DB::raw('MAX(utilization.created_by) as created_by'), DB::raw('MAX(utilization.facility_id) as facility_id'),
                DB::raw('MAX(utilization.fundsource_id) as fundsource_id'), DB::raw('MAX(utilization.transfer_id) as transfer_id'),
                DB::raw('MAX(utilization.status) as status'), DB::raw('MAX(utilization.id) as id'))
            ->groupBy(DB::raw('CASE WHEN utilization.div_id = 0 THEN utilization.id ELSE utilization.div_id END'))
            ->leftJoin('proponent', 'proponent.id', '=', 'utilization.proponentinfo_id')
            ->with('fundSourcedata')
            ->with('facilitydata:id,name,address')
            ->with('user:id,userid,fname,lname,mname')
            ->orderBy('id', 'asc')
            ->get();
        $proponent = Proponent::where('pro_group', $pro_group)->first();

        // excel file before
        $title = $proponent->proponent;
        // $filename = $title.'.xls';
        // header("Content-Type: application/xls");
        // header("Content-Disposition: attachment; filename=$filename");
        // header("Pragma: no-cache");
        // header("Expires: 0");
        // $table_body = "<tr>
        //         <th>Route No</th>
        //         <th>SAA</th>
        //         <th>Facility</th>
        //         <th>Allocation</th>
        //         <th>Utilize Amount</th>
        //         <th>Percentage</th>
        //         <th>Discount</th>
        //         <th>Balance</th>
        //         <th>Patients</th>
        //         <th>Created On</th>
        //     </tr>";


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Adjust column widths
        $sheet->getColumnDimension('A')->setWidth(2);  
        $sheet->getColumnDimension('B')->setWidth(20); 
        $sheet->getColumnDimension('C')->setWidth(30); 
        $sheet->getColumnDimension('D')->setWidth(55);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20); 
        $sheet->getColumnDimension('G')->setWidth(20); 
        $sheet->getColumnDimension('H')->setWidth(20); 
        $sheet->getColumnDimension('I')->setWidth(20); 
        $sheet->getColumnDimension('J')->setWidth(30); 
        $sheet->getColumnDimension('K')->setWidth(20); 

        $sheet->mergeCells("B1:D1");
        $richText1 = new RichText();
        $normalText = $richText1->createTextRun($title);
        $normalText->getFont()->setBold(true)->setSize(20); 
        $sheet->setCellValue('B1', $richText1);
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getStyle('B1')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("ROUTE NO");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('B3', $richText1);
        $sheet->getStyle('B3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("SAA NO");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('C3', $richText1);
        $sheet->getStyle('C3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("FACILITY");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('D3', $richText1);
        $sheet->getStyle('D3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("ALLOCATION");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('E3', $richText1);
        $sheet->getStyle('E3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("UTILIZED AMOUNT");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('F3', $richText1);
        $sheet->getStyle('F3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("PERCENTAGE");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('G3', $richText1);
        $sheet->getStyle('G3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("DISCOUNT");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('H3', $richText1);
        $sheet->getStyle('H3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("BALANCE");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('I3', $richText1);
        $sheet->getStyle('I3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("PATIENTS");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('J3', $richText1);
        $sheet->getStyle('J3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("CREATED ON");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('K3', $richText1);
        $sheet->getStyle('K3')->getAlignment()->setWrapText(true);

        $sheet->getStyle('B3:K3')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(3)->setRowHeight(50); 

        $data = [];

        $all = ProponentInfo::whereIn('proponent_id', $proponentIds)->get();
        $all_id = ProponentInfo::whereIn('proponent_id', $proponentIds)->pluck('id')->toArray();

        $allocation_funds = $all->sum(function ($info) {
            return (float) str_replace(',', '', $info->alocated_funds);
        });  

        $deduct = 0;
        if($utilization){

            foreach($utilization as $row) {

                if($row->status != 1){
                    $user = $row->user->lname .', '. $row->user->fname .' '. $row->user->mname;
                    $created_on = date('F j, Y', strtotime($row->created_at));
                    $saa = $row->fundSourcedata->saa;

                    if($row->route_no == 0){
                        // to be open and recalculated once it is finalize to transfer funds to another proponent
                        // $transfer = Transfer::where('id', $row->transfer_id)->with('to_fundsource')->with('from_fundsource')->with('to_facilityInfo')->with('from_facilityInfo')->first();
                        
                        // $from_fac = array_map(fn($value) => (int)$value, json_decode($transfer->from_facility));
                        // $from_facilities =  Facility::whereIn('id', $from_fac)->pluck('name')->toArray();
                        // $to_fac = array_map(fn($value) => (int)$value, json_decode($transfer->to_facility));
                        // $to_facilities =  Facility::whereIn('id', $to_fac)->pluck('name')->toArray();

                        // if($row->status == 2){ //2-deducted 
                        //     $allocation_funds = $allocation_funds + str_replace(',','', $transfer->from_amount);
                        //     $rem_bal = $allocation_funds - str_replace(',','', $transfer->from_amount);
                        //     $transfer_rem = 'Transfer (deducted)';
                        //     // $allocation_funds = 
                        // }else if($row->status == 3){ // 3 -added
                        //     $allocated = $allocation_funds - str_replace(',','', $transfer->to_amount);
                        //     $rem_bal = $allocation_funds + str_replace(',','', $transfer->to_amount);
                        //     $transfer_rem = 'Transfer (added)';
                        // }
                        // // return $from_facilities;

                        // $facility_new = 'from '. $transfer->from_fundsource->saa.' - '.  implode(',', $from_facilities).' to '. $transfer->to_fundsource->saa.' - '.  implode(',', $to_facilities);
                        // $table_body .= "<tr>
                        //     <td style='vertical-align:top;'>$transfer_rem</td>
                        //     <td style='vertical-align:top;'>$saa</td>
                        //     <td style='vertical-align:top;'>$facility_new</td>
                        //     <td style='vertical-align:top;'>$allocated</td>
                        //     <td style='vertical-align:top;'>$row->utilize_amount</td>
                        //     <td style='vertical-align:top;'></td>
                        //     <td style='vertical-align:top;'></td>
                        //     <td style='vertical-align:top;'>$rem_bal</td>
                        //     <td style='vertical-align:top;'></td>
                        //     <td style='vertical-align:top;'>$created_on</td>
                        // </tr>";
                    }else{
                        $facility = $row->facilitydata->name;
                        $dv = Dv::where('route_no', $row->route_no)->first();
                        $dv3 = Dv3::where('route_no', $row->route_no)->with('extension')->first();
                        $new_dv = NewDV::where('route_no', $row->route_no)->first();

                        if($dv){
                            $saa_Ids = json_decode($dv->fundsource_id);
                            $saa_name = Fundsource::whereIn('id',$saa_Ids)->pluck('saa')->toArray();
                            $saaString = implode("\n", $saa_name);
                            $groupIdArray = explode(',', $dv->group_id);
                            $patients = Patients::whereIn('group_id', $groupIdArray)->get();
                            $patient_list = [];
                            foreach($patients as $patient){
                                $patient_list[] = $patient->lname.', '. $patient->fname .' '. $patient->mname;
                            }
                            $string_patient =  implode("\n", $patient_list);
                            $trap = 1;
                            if($dv->deduction1 >3){
                                $trap = 1.12;
                            }
                            $amount1 = str_replace(',', '', $dv->amount1);
                            $amount2 = str_replace(',', '', $dv->amount2);
                            $amount3 = str_replace(',', '', $dv->amount3);
                
                            $discount1 = !empty($dv->amount1)? floatval($amount1/$trap * $dv->deduction1/100) + floatval($amount1/$trap * $dv->deduction2/100) :'';
                            $discount2 = !empty($dv->amount2)? floatval($amount2/$trap * $dv->deduction1/100) + floatval($amount1/$trap * $dv->deduction2/100) :'';
                            $discount3 = !empty($dv->amount3)? floatval($amount3/$trap * $dv->deduction1/100) + floatval($amount1/$trap * $dv->deduction2/100) :'';
                
                            $amounts = array_filter([
                                $dv->amount1 !== null ? $dv->amount1 : null,
                                $dv->amount2 !== null ? $dv->amount2 : null,
                                $dv->amount3 !== null ? $dv->amount3 : null,
                            ]);
                            $discounts = array_filter([
                                $discount1 !== null ? $discount1 : null,
                                $discount2 !== null ? $discount2 : null,
                                $discount3 !== null ? $discount3 : null,
                            ]);
                            $all_amount = implode("\n", $amounts);
                            $rem_bal =  $allocation_funds - str_replace(',','', $dv->total_amount);
                            $discount_all = implode("\n", $discounts);
                            $percentage = number_format((str_replace(',', '', $dv->total_amount) / $allocation_funds) * 100, 2);
                            $al_disp = number_format($allocation_funds, 2);
                            $rem_disp = number_format($rem_bal, 2);
                        }else if($dv3){

                            $saa_ids = [];
                            $amounts = [];
                            $amount_total = 0;

                            foreach($dv3->extension as $row){

                                if (in_array($row->info_id, $all_id)) {
                                    $saa_ids [] = $row->fundsource_id;
                                    $amounts [] = $row->amount;
                                    $amount_total = $amount_total + $row->amount;
                                }
                            }

                            $saa_name = Fundsource::whereIn('id',$saa_ids)->pluck('saa')->toArray();
                            $saaString = implode("\n", $saa_name);
                            $all_amount = implode("\n", $amounts);
                            $rem_bal =  $allocation_funds - str_replace(',','', $amount_total);
                            $percentage = number_format((str_replace(',', '', $amount_total) / $allocation_funds) * 100, 2);
                            $al_disp = number_format($allocation_funds, 2);
                            $rem_disp = number_format($rem_bal, 2);
                            $discount_all = 0;
                            $string_patient = '';
                        }elseif($new_dv){

                            $saa_ids = [];
                            $amounts = [];
                            $amount_total = 0;

                            $util = Utilization::where('div_id', $row->route_no)->where('status', 0)->whereIn('proponent_id', $proponentIds)->get();
                       
                            foreach($util as $u){
                                $saa_ids [] = $u->fundsource_id;
                                $amounts [] = number_format(str_replace(',','', $u->utilize_amount), 2,'.',',');
                                $amount_total = $amount_total +str_replace(',','', $u->utilize_amount);
                            }
                           
                            $saa_name = Fundsource::whereIn('id',$saa_ids)->pluck('saa')->toArray();
                            $saaString = implode("\n", $saa_name);
                            $all_amount = implode("\n", $amounts);
                            $rem_bal =  $allocation_funds - str_replace(',','', $amount_total);
                            $percentage = number_format((str_replace(',', '', $amount_total) / $allocation_funds) * 100, 2);
                            $al_disp = number_format($allocation_funds, 2);
                            $rem_disp = number_format($rem_bal, 2);
                            $discount_all = 0;
                            $string_patient = '';
                            // $saaString = str_replace('<br>', "\n", $saaString);
                            // $all_amount = str_replace('<br>', "\n", $all_amount);

                        }

                        $data[] = [
                            $row->route_no,
                            $saaString,
                            $facility,
                            str_replace(',','',$al_disp),
                            str_replace(',','',$all_amount),
                            $percentage. " %",
                            str_replace(',','',$discount_all),
                            str_replace(',','',$rem_disp),
                            $string_patient,
                            $created_on
                        ];
                        // $table_body .= "<tr>
                        //     <td style='vertical-align:top;'>$row->route_no</td>
                        //     <td style='vertical-align:top;'>$saaString</td>
                        //     <td style='vertical-align:top;'>$facility</td>
                        //     <td style='vertical-align:top;'>$al_disp</td>
                        //     <td style='vertical-align:top;'>$all_amount</td>
                        //     <td style='vertical-align:top;'>$percentage %</td>
                        //     <td style='vertical-align:top;'>$discount_all</td>
                        //     <td style='vertical-align:top;'>$rem_disp</td>
                        //     <td style='vertical-align:top;'>$string_patient</td>
                        //     <td style='vertical-align:top;'>$created_on</td>
                        // </tr>";
                        $allocation_funds = $rem_bal;
                    }
                }
            }
        }else{
            // $table_body .= "<tr>
            //     <td colspan=6 style='vertical-align:top;'>No Data Available</td>
            // </tr>";
        }
        // $display =
        //     '<h1>'.$title.'</h1>'.
        //     '<table cellspacing="1" cellpadding="5" border="1">'.$table_body.'</table>';

        // return $display;
        // return $data;
        $sheet->fromArray($data, null, 'B4');
        $sheet->getStyle('E4:F' . (count($data) + 3))
            ->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('H4:I' . (count($data) + 3))
            ->getNumberFormat()->setFormatCode('#,##0.00');
            

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER, 
                'wrapText' => true, // Enables wrapping
            ],
        ];
        
        $sheet->getStyle('B3:K' . (count($data) + 3))->applyFromArray($styleArray);
        $sheet->getStyle('B4:K' . (count($data) + 3))->getAlignment()->setWrapText(true);

        
        $sheet->getStyle('B4:D' . (count($data) + 3))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        
        $sheet->getStyle('E4:I' . (count($data) + 3))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->getStyle('J4:K' . (count($data) + 3))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            
        // Output preparation
        ob_start();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        // Filename
        $filename = $title . date('Ymd') . '.xlsx';

        // Set headers
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Output the file
        return $xlsData;
        exit;
    }
    public function getFacilityReport($facility_id){
        $utilize = Utilization::
                        where('facility_id', $facility_id)
                    ->orWhereJsonContains('facility_id', $facility_id)
                    ->with('facilitydata:id,name', 'fundSourcedata', 'proponentdata:id,proponent')->where('status', '<>', '1')->get();
        $title = Facility::where('id', $facility_id)->value('name');
        // $filename = $title.'.xls';
        // header("Content-Type: application/xls");
        // header("Content-Disposition: attachment; filename=$filename");
        // header("Pragma: no-cache");
        // header("Expires: 0");
        // $table_body = "<tr>
        //         <th>Fundsource</th>
        //         <th>Proponent</th>
        //         <th>Utilize Amount</th>
        //         <th>Discount</th>
        //         <th>Remarks</th>
        //         <th>Created On</th>
        //     </tr>";

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Adjust column widths
        $sheet->getColumnDimension('A')->setWidth(2);  
        $sheet->getColumnDimension('B')->setWidth(30); 
        $sheet->getColumnDimension('C')->setWidth(40); 
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(30); 
        $sheet->getColumnDimension('G')->setWidth(20); 
        $sheet->getColumnDimension('H')->setWidth(20);  

        $sheet->mergeCells("B1:D1");
        $richText1 = new RichText();
        $normalText = $richText1->createTextRun($title);
        $normalText->getFont()->setBold(true)->setSize(20); 
        $sheet->setCellValue('B1', $richText1);
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getStyle('B1')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("FUNDSOURCE");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('B3', $richText1);
        $sheet->getStyle('B3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("PROPONENT");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('C3', $richText1);
        $sheet->getStyle('C3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("UTILIZED AMOUNT");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('D3', $richText1);
        $sheet->getStyle('D3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("DISCOUNT");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('E3', $richText1);
        $sheet->getStyle('E3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("REMARKS");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('F3', $richText1);
        $sheet->getStyle('F3')->getAlignment()->setWrapText(true);

        $richText1 = new RichText();
        $normalText = $richText1->createTextRun("CREATED ON");
        $normalText->getFont()->setBold(true); 
        $sheet->setCellValue('G3', $richText1);
        $sheet->getStyle('G3')->getAlignment()->setWrapText(true);

        $sheet->getStyle('B3:G3')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(3)->setRowHeight(50); 

        $data = [];

        if($utilize){
            foreach($utilize as $row){
                $created_on = date('F j, Y', strtotime($row->created_at));
                // return $row->created_at;
                $saa = $row->fundSourcedata->saa;
                $proponent = $row->proponentdata ? $row->proponentdata->proponent :'';
                $discount = $row->discount;
                $utilize = $row->utilize_amount;
                $remarks = "processed";
                if($row->status ==2){ //from

                    $transfer = Transfer::where('id', $row->transfer_id)->with('to_fundsource','to_facilityInfo','to_proponentInfo')->first();

                    if(is_string($transfer->from_facility)){
                        $facilities = json_decode($transfer->from_facility, true); 
                    
                        if (!is_array($facilities)) {
                            $facilities = [$facilities]; 
                        }
                    
                        $facility_n = Facility::whereIn('id', array_map('intval', $facilities))->pluck('name')->toArray();
                        $facility_n = implode(', ', $facility_n);
                    } else {
                        $facility_n = Facility::where('id', $transfer->from_facility)->value('name');
                    }

                    if($transfer->to_proponentInfo == null){
                        $pro_name = '';
                    }else{
                        $pro_name = $transfer->to_proponentInfo->proponent;
                    }
                    $remarks = 'transferred to '.$pro_name.' - '.$transfer->to_fundsource->saa.' - '. $facility_n;

                }else if($row->status ==3){ //to

                    $transfer = Transfer::where('id', $row->transfer_id)
                        ->with('from_fundsource', 'from_facilityInfo', 'from_proponentInfo')
                        ->first();

                    if (is_string($transfer->to_facility)) {
                        $facilities = json_decode($transfer->to_facility, true); 

                        if (!is_array($facilities)) {
                            $facilities = [$facilities]; 
                        }

                        $facility_n = Facility::whereIn('id', array_map('intval', $facilities))->pluck('name')->toArray();
                        $facility_n = implode(', ', $facility_n);
                    } else {
                        $facility_n = Facility::where('id', $transfer->to_facility)->value('name');
                    }

                    if($transfer->from_proponentInfo == null){
                        $pro_name = '';
                    }else{
                        $pro_name = $transfer->from_proponentInfo->proponent;
                    }
                    $remarks = 'transferred from '.$pro_name.' - '.$transfer->from_fundsource->saa.' - '. $facility_n;
                }
                // $table_body .= "<tr>
                //     <td style='vertical-align:top;'>$saa</td>
                //     <td style='vertical-align:top;'>$proponent</td>
                //     <td style='vertical-align:top;'>$utilize</td>
                //     <td style='vertical-align:top;'>$discount</td>
                //     <td style='vertical-align:top;'>$remarks</td>
                //     <td style='vertical-align:top;'>$created_on</td>
                //     </tr>";
                $data[]=[
                    $saa,
                    $proponent,
                    $utilize ? str_replace(',','',$utilize) : 0.00,
                    $discount > 0 ? str_replace(',','',$discount) : 0.00,
                    $remarks,
                    $created_on
                ];
            }
        }

        $sheet->fromArray($data, null, 'B4');
        $sheet->getStyle('D4:E' . (count($data) + 3))
            ->getNumberFormat()->setFormatCode('#,##0.00');

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER, 
            ],
        ];
        
        $sheet->getStyle('B3:G' . (count($data) + 3))->applyFromArray($styleArray);
        $sheet->getStyle('B4:G' . (count($data) + 3))->getAlignment()->setWrapText(true);

        $sheet->getStyle('B4:C' . (count($data) + 3))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        
        $sheet->getStyle('D4:E' . (count($data) + 3))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->getStyle('F4:G' . (count($data) + 3))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            
        // Output preparation
        ob_start();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        // Filename
        $filename = $title . date('Ymd') . '.xlsx';

        // Set headers
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Output the file
        return $xlsData;
        exit;
        
        // $display =
        //     '<h1>'.$title.'</h1>'.
        //     '<table cellspacing="1" cellpadding="5" border="1">'.$table_body.'</table>';

        // return $display;
    }

    public function updateAmount($patientId, $amount){

        $patient = Patients::find($patientId);
        $newAmount = str_replace(',', '',$amount);

        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }else{
            if($patient->group_id !== null && $patient->group_id !== ""){
                $group = Group::where('id', $patient->group_id)->first();
                $updated_a = floatval(str_replace(',', '', $group->amount)) - floatval($patient->actual_amount) + floatval($newAmount);
                $stat = $group->status;
                $group->status = 1;
                $group->amount = number_format($updated_a, 2, '.',',');
                $group->save();
            }
            $patient->actual_amount = $newAmount;
            $patient->save();
            // session()->flash('actual_amount', true);
        }
    }
    
    public function group(Request $request){ 
        $groups = Group::with('proponent', 'facility', 'user', 'patient')
            ->when(
            !$request->viewAll && $request->filled('keyword'),
            function ($query) use ($request) {
                $query->where(function ($subquery) use ($request) {
                    $subquery->whereHas('proponent', function ($proponentQuery) use ($request) {
                        $proponentQuery->where('proponent', 'LIKE', "%{$request->keyword}%");
                    })
                    ->orWhereHas('patient', function ($patientQuery) use ($request) {
                        $patientQuery->where('lname', 'LIKE', "%{$request->keyword}%");
                    });
                });
            }
        )
        ->withCount('patient')
        ->orderBy('id', 'desc')
        ->paginate(50); 
        
        if($request->viewAll){
            $request->keyword ='';
        } 
        return view('group.group', ['groups'=>$groups, 'keyword'=>$request->keyword]);
    }
  
    public function getPatientGroup($group_id){
        $patient_list = Patients::where('group_id', $group_id)->with('muncity')->with('province')->with('barangay')->get();
        return view('group.patients_group', ['patient_list'=>$patient_list, 'group'=>Group::where('id', $group_id)->first()]);
    }

    public function getPatient($patient_id){
        $patient = Patients::where('id', $patient_id)->first();
        $group = Group::where('id', $patient->group_id)->first();
        $amount = str_replace(',','',$group->amount) - str_replace(',','', $patient->actual_amount);
        $stat = $group->status;
        $group->status = 1;
        $group->amount = $amount;
        $group->save();
        $patient->group_id = null;
        $patient->save();   
        if($stat == 0){
            session()->flash('update_group', true);
        }
          session()->flash('remove_patientgroup', true); 
    }
    public function getPatients($facility_id, $proponent_id){
        return Patients::where(function($query) {
            $query->whereNull('group_id')
                  ->orWhere('group_id', '=', '');
        })
        ->whereNotNull('actual_amount')
        ->where('facility_id', $facility_id)
        ->where('proponent_id', $proponent_id)
        ->where('actual_amount', '!=', 0)
        ->get();

    }

    public function updateGroupList(Request $request){
        
        $patient = Patients::where('id', $request->input('fac_id'))->first();
        $group = Group::where('id', $request->input('group_id'))->first();
        $amount = str_replace(',','',$group->amount) + str_replace(',','', $patient->actual_amount);
        $group->amount = $amount;
        $stat = $group->status;
        $group->status = 1;
        $group->save();
        $patient->group_id = $request->input('group_id');
        $patient->save();
        return redirect()->back()->with('save_patientgroup', true);
        if($stat == 0){
            session()->flash('update_group', true);
        }
    }

    public function saveGroup(Request $request){

        $patients = $request->input('group_patients');
        $patientsArray = explode(',', $patients);
        $group = new Group();
        $group->facility_id = $request->input('group_facility');
        $group->proponent_id = $request->input('group_proponent');
        $group->grouped_by = Auth::user()->userid;
        $group->amount = $request->input('group_amountT');
        $group->status = 1;
        $group->save();
        Patients::whereIn('id', $patientsArray)->update(['group_id' => $group->id]);
        return redirect()->back()->with('save_group', true);
    }

    public function createPatientSave(Request $request) {
        $data = $request->all();
        $patient = Patients::create($data);
        $patientCount = Patients::where('fname', $request->fname)
            ->where('lname', $request->lname)
            ->where('mname', $request->mname)
            ->where('region', $request->region)
            ->where('province_id', $request->province_id)
            ->where('muncity_id', $request->muncity_id)
            ->where('barangay_id', $request->barangay_id)
            ->count();
        if($patientCount>0){
            session()->flash('patient_exist', $patientCount);
        }else{
            session()->flash('patient_save', true);
        }

        $util = new ProponentUtilizationV1();
        $util->patient_id = $patient->id;
        $util->proponent_id = $patient->proponent_id;
        $util->amount = (float) str_replace(',','', $patient->guaranteed_amount);
        $util->proponent_code = Proponent::where('id', $patient->proponent_id)->value('proponent_code');
        $util->save();

        return redirect()->back();
    }

    public function fetchPatient($id){
        $patient =  Patients::where('id',$id)
                        ->with(
                            [
                                'muncity' => function ($query) {
                                    $query->select(
                                        'id',
                                        'description'
                                    );
                                },
                                'barangay' => function ($query) {
                                    $query->select(
                                        'id',
                                        'description'
                                    );
                                },
                                'fundsource',
                            ])->orderBy('updated_at', 'desc')
                        ->first();

        $municipal = Muncity::select('id', 'description')->get();
        $barangay = Barangay::select('id', 'description')->get();
        return [
            'patient' => $patient
        ];        
    }

    //sir jondy unused
    public function editPatient(Request $request) {
        $patient =  Patients::where('id',$request->patient_id)
                        ->with(
                            [
                                'muncity' => function ($query) {
                                    $query->select(
                                        'id',
                                        'description'
                                    );
                                },
                                'barangay' => function ($query) {
                                    $query->select(
                                        'id',
                                        'description'
                                    );
                                },
                                'fundsource',
                            ])->orderBy('updated_at', 'desc')
                        ->first();

        $municipal = Muncity::select('id', 'description')->get();
        $barangay = Barangay::select('id', 'description')->get();
        return view('maif.update_patient',[
            'provinces' => Province::get(),
            'fundsources' => Fundsource::get(),
            'proponents' => Proponent::get(),
            'facility' => Facility::get(),
            'patient' => $patient,
            'municipal' => $municipal,
            'barangay' => $barangay,
        ]);
    }

    public function mailHistory($id){
        return view('maif.mail_history',[
            'history' => MailHistory::where('patient_id', $id)->with('patient', 'sent', 'modified')->get()
        ]);
    }

    public function patientHistory($id){
        return view('maif.patient_history',[
            'logs' => PatientLogs::where('patient_id', $id)->with([
                'modified', 'facility:id,name', 'province', 'muncity', 'barangay', 'proponent:id,proponent', 'proponent_from:id,proponent'
            ])->get()
        ]);
    }
 
    public function updatePatient($id, Request $request){
        $val = $request->input('update_send');
        $patient_id = $id;
        $patient = Patients::where('id', $patient_id)->first();

        if(!$patient){
            return redirect()->back()->with('error', 'Patient not found');
        }

        DB::beginTransaction();

        $patientLogs = new PatientLogs();
        $patientLogs->patient_id = $patient->id;
        $patientLogs->fill(Arr::except($patient->toArray(), ['status', 'sent_type', 'user_type', 'transd_id', 'fc_status', 'expired', 'pro_used', 'rtrv_remarks']));
        unset($patientLogs->id);
        $patientLogs->save();
        
        session()->flash('patient_update', true);
        $patient->fname = $request->input('fname');
        $patient->lname = $request->input('lname');
        $patient->mname = $request->input('mname');
        $patient->dob   = $request->input('dob');
        $patient->region = $request->input('region');

        if($patient->region !== "Region 7"){
            $patient->other_province = $request->input('other_province');
            $patient->other_muncity = $request->input('other_muncity');
            $patient->other_barangay = $request->input('other_barangay');
        }

        $patient->date_guarantee_letter = $request->input('date_guarantee_letter');
        $patient->province_id = $request->input('province_id');
        $patient->muncity_id  = $request->input('muncity_id');
        $patient->barangay_id = $request->input('barangay_id');
        // $patient->fundsource_id = $request->input('fundsource_id');
        $patient->proponent_id = $request->input('proponent_id');
        $patient->facility_id = $request->input('facility_id');
        $patient->patient_code = $request->input('patient_code');
        $patient->guaranteed_amount = $request->input('guaranteed_amount');
        $patient->actual_amount = $request->input('actual_amount');
        $patient->remaining_balance = $request->input('remaining_balance');
        $patient->pat_rem = $request->input('pat_rem');
        $patient->sent_type = $request->input('sent_type');
        $patient->save();
        DB::commit();
        if($val == "upsend"){
            // return Patients::where('id', $patient->id)->first();
            return redirect()->route('patient.sendpdf', ['patientid' => $patient->id]);
        }else{
            return redirect()->back();
        }

        return redirect()->back()->with('patient_update', true);

    }

    public function removePatient($id){
        if($id){
            Patients::where('id', $id)->delete();
        }
        return redirect()->back()->with('remove_patient', true);
    }

    public function groupRemovePatient($id){
        $pat = Patients::where('id', $id)->first();
        if($pat){
            $gr = Group::where('id', $pat->group_id)->first();
            $gr->amount = (double)str_replace(',', '', $gr->amount) - (double)str_replace(',', '', $pat->actual_amount);
            $gr->save();
            $pat->group_id = null;
            $pat->save();
        }
    }

    public function muncityGet(Request $request) {
        return Muncity::where('province_id',$request->province_id)->whereNull('vaccine_used')->get();
    }

    public function barangayGet(Request $request) {
        return Barangay::where('muncity_id',$request->muncity_id)->get();
    }

    public function transactionGet() {
        $facilities = Facility::where('hospital_type','private')->get();
        return view('fundsource.transaction',[
            'facilities' => $facilities
        ]);
    }

    public function facilitySend($id){

        Patients::where('id', $id)->update(['status' => 1]);
        return redirect()->back()->with('facility_send',true);
        
    }   

    public function returnPatient($id, Request $request){
        $patient_id = $id;
        $patient = Patients::where('id', $id)->first();

        if(!$patient){
            return redirect()->back()->with('error', 'Patient not found');
        }
        Patients::where('id', $id)->update([
            'sent_type' => $request->sent_type, 
            'fc_status' => $request->sent_type == 3 ? 'referred' : null, 
        ]);
        ReturnedPatients::insert([
            'patient_id' => $id, 
            'remarks' => $request->pat_rem,
            'status' => $request->sent_type,
            'remarks_by' => Auth::user()->userid
        ]);
        
        return redirect()->back()->with($request->sent_type == 2? 'return_gl' : 'process_gl', true);
    }

    public function acceptPat($id){
        Patients::where('id', $id)->update([
            'fc_status' => 'referred',
            'sent_type' => 3
        ]);
        return redirect()->back()->with('process_gl', true);
    }
    
    public function retrievePat($id, $remarks){
        Patients::where('id', $id)->update([
            'fc_status' => 'retrieved',
            'rtrv_remarks' => $remarks
        ]);
        return response()->json(['status' => 'success']);
    }

    public function exportToExcel(Request $request){
      
        $keyword = $request->input('keyword'); 
        $filter = $request->input('filter'); 
        $received = $request->input('received'); 
        
        $query = Logbook::with('r_by');


    if (!empty($keyword)) {
        $query->where('control_no', 'LIKE', '%' . $keyword . '%');

    } elseif (!empty($received)) {
        $query->whereHas('r_by', function ($q) use ($received) {
            $names = is_array($received)
                ? $received
                : array_filter(array_map('trim', explode(',', $received)));

            foreach ($names as $index => $name) {
                $method = $index === 0 ? 'whereRaw' : 'orWhereRaw';
                $q->$method("CONCAT(fname, ' ', lname) LIKE ?", ["%{$name}%"]);
            }
        });

    } elseif (!empty($filter)) {
        $query->where('received_by', $filter);
    }



        $logbook = $query->orderBy('received_on', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(25);

        $sheet->getStyle('A1:D1')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $headers = [
            'A1' => 'CONTROL NO',
            'B1' => 'DELIVERED BY',
            'C1' => 'RECEIVED BY',
            'D1' => 'RECEIVED ON'
        ];

        foreach ($headers as $cell => $headerText) {
            $richText = new RichText();
            $normalText = $richText->createTextRun($headerText);
            $normalText->getFont()->setBold(true);
            $sheet->setCellValue($cell, $richText);
            $sheet->getStyle($cell)->getAlignment()->setWrapText(true);
        }
   
        $data = [];
        foreach ($logbook as $entry) {
            $receiverName = $entry->r_by ? 
                trim($entry->r_by->fname . ' ' . $entry->r_by->lname) : 'N/A';
            
            $data[] = [
                $entry->control_no ?? '',
                $entry->delivered_by ?? '',
                $receiverName,
                $entry->received_on ? date('F j, Y', strtotime($entry->received_on)) : ''
            ];
        }

        $sheet->fromArray($data, null, 'A2');

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $lastRow = count($data) + 1;
        $sheet->getStyle("A1:D{$lastRow}")->applyFromArray($styleArray);
        $sheet->getStyle("A2:D{$lastRow}")->getAlignment()->setWrapText(true);

        $sheet->getStyle("A2:A{$lastRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B2:C{$lastRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("D2:D{$lastRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $filename = "Logbook Summary" . date('Ymd') . ".xlsx";

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0");
        header("Pragma: no-cache");
        header("Expires: 0");

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    public function patientsSAmple(Request $request){

        $filter_date = $request->input('filter_dates');
        $order = $request->input('order', 'asc');


        $patients = Patients::with([
            'province:id,description',
            'muncity:id,description',
            'barangay:id,description',
            'encoded_by:userid,fname,lname,mname',
            'gl_user:username,fname,lname',
            'facility:id,name',
            'proponentData:id,proponent',
            'pat_remarks:patient_id,remarks'
        ])->whereNotNull('sent_type');
       
        //  -- for date range
        if($request->gen){
            $dateRange = explode(' - ', $filter_date);
            $start_date = date('Y-m-d', strtotime($dateRange[0]));
            $end_date = date('Y-m-d', strtotime($dateRange[1]));
            $patients = $patients ->whereBetween('created_at', [$start_date, $end_date . ' 23:59:59'])->whereNotNull('sent_type');
        }

        // -- for search

        if($request->viewAll){

            $request->keyword = '';
            $request->filter_date = '';
            $request->filter_fname = '';
            $request->filter_mname = '';
            $request->filter_lname = '';
            $request->filter_facility = '';
            $request->filter_proponent = '';
            $request->filter_code = '';
            $request->filter_region = '';
            $request->filter_province = '';
            $request->filter_muncity = '';
            $request->filter_barangay = '';
            $request->filter_on = '';
            $request->filter_by = '';
            $filter_date = '';
            $request->gen = '';


        }else if($request->keyword){
            // return $patients->where('pro_used', null)->orderBy('id', 'desc')->paginate(50);

            $keyword = $request->keyword;
            $patients = $patients->where(function ($query) use ($keyword) {
                $query->whereNotNull('pro_used')
                    ->where(function ($query) use ($keyword) {
                        $query->where('fname', 'LIKE', "%$keyword%")
                            ->orWhere('lname', 'LIKE', "%$keyword%")
                            ->orWhere('mname', 'LIKE', "%$keyword%")
                            ->orWhere('region', 'LIKE', "%$keyword%")
                            ->orWhere('other_province', 'LIKE', "%$keyword%")
                            ->orWhere('other_muncity', 'LIKE', "%$keyword%")
                            ->orWhere('other_barangay', 'LIKE', "%$keyword%")
                            ->orWhere('patient_code', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('facility', function ($query) use ($keyword) {
                        $query->where('name', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('proponentData', function ($query) use ($keyword) {
                        $query->where('proponent', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('barangay', function ($query) use ($keyword) {
                        $query->where('description', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('muncity', function ($query) use ($keyword) {
                        $query->where('description', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('province', function ($query) use ($keyword) {
                        $query->where('description', 'LIKE', "%$keyword%");
                    });
            });
        }

        // -- for table header sorting

        if ($request->sort && $request->input('sort') == 'facility') {
            $patients = $patients->sortable(['facility.name' => 'asc'])->whereNotNull('sent_type');
        }else if ($request->sort && $request->input('sort') == 'proponent') {
            $patients = $patients->sortable(['proponentData.proponent' => 'asc'])->whereNotNull('sent_type');
        }else if ($request->sort && $request->input('sort') == 'province') {
            
            $patients = $patients->leftJoin('province', 'province.id', '=', 'patients.province_id')
                            ->whereNotNull('sent_type')
                            ->orderBy('patients.other_province', $request->input('order'))
                            ->orderBy('province.description', $request->input('order')) 
                            ->select('patients.*');

        }else if ($request->sort && $request->input('sort') == 'municipality') {
            
            $patients = $patients->leftJoin('muncity', 'muncity.id', '=', 'patients.muncity_id')
                            ->whereNotNull('pro_used')
                            ->orderBy('patients.other_muncity', $request->input('order'))
                            ->orderBy('muncity.description', $request->input('order')) 
                            ->select('patients.*');

        }else if ($request->sort && $request->input('sort') == 'barangay') {
            
            $patients = $patients->leftJoin('barangay', 'barangay.id', '=', 'patients.barangay_id')
                            ->whereNotNull('pro_used')
                            ->orderBy('patients.other_barangay', $request->input('order'))
                            ->orderBy('barangay.description', $request->input('order')) 
                            ->select('patients.*');

        }else if ($request->sort && $request->input('sort') == 'encoded_by') {
        
            $patients = $patients
                        ->whereNotNull('pro_used')
                        ->orderBy(
                            \DB::connection('dohdtr')
                                ->table('users')
                                ->select('lname')
                                ->whereColumn('users.userid', 'patients.created_by'),
                                $request->input('order')
                        );
        }else{
            $patients->sortable(['id' => 'desc']);
        }
        // for filtering column

        // if($request->filter_col){
            if($request->filter_date){
                $patients = $patients->whereIn('date_guarantee_letter', explode(',',$request->filter_date))->whereNotNull('sent_type');
            }
            if($request->filter_fname){
                $patients = $patients->whereIn('fname', explode(',',$request->filter_fname))->whereNotNull('sent_type');
            }
            if($request->filter_mname){
                $patients = $patients->whereIn('mname', explode(',',$request->filter_date))->whereNotNull('sent_type');
            }
            if($request->filter_lname){
                $patients = $patients->whereIn('lname', explode(',',$request->filter_lname))->whereNotNull('sent_type');
            }
            if($request->filter_facility){
                $patients = $patients->whereIn('facility_id', explode(',',$request->filter_facility))->whereNotNull('sent_type');
            }
            if($request->filter_proponent){
                $patients = $patients->whereIn('proponent_id', explode(',',$request->filter_proponent))->whereNotNull('sent_type');
            }
            if($request->filter_code){
                $patients = $patients->whereIn('patient_code', explode(',',$request->filter_code))->whereNotNull('sent_type');
            }
            if($request->filter_region){
                $patients = $patients->whereIn('region', explode(',',$request->filter_region))->whereNotNull('sent_type');
            }
            if($request->filter_province){
                $patients = $patients->whereIn('province_id', explode(',',$request->filter_province))
                            ->orWhereIn('other_province', explode(',',$request->filter_province))
                            ->whereNotNull('sent_type');
            }
            if($request->filter_municipality){
                $patients = $patients->whereIn('muncity_id', explode(',',$request->filter_municipality))
                            ->orWhereIn('other_muncity', explode(',',$request->filter_municipality))
                            ->whereNotNull('sent_type');
            }
            if($request->filter_barangay){
                $patients = $patients->whereIn('barangay_id', explode(',',$request->filter_barangay))
                            ->orWhereIn('other_barangay', explode(',',$request->filter_barangay))
                            ->whereNotNull('sent_type');
            }
            if($request->filter_on){
                $patients = $patients->whereIn(DB::raw('DATE(created_at)'), explode(',',$request->filter_on))->whereNotNull('sent_type');
                // return  $request->filter_on;
            }
            if($request->filter_by){
                // return explode(',',$request->filter_by);
                $patients = $patients->whereIn('created_by', explode(',',$request->filter_by))->whereNotNull('sent_type');
            }
        // }

        $date = clone ($patients);
        $fname = clone ($patients);
        $mname = clone ($patients);
        $lname = clone ($patients);
        $facs = clone ($patients);
        $code = clone ($patients);
        $proponent = clone ($patients);
        $region = clone ($patients);
        $province = clone ($patients);
        $muncity = clone ($patients);
        $barangay = clone ($patients);
        $on = clone ($patients);
        $by = clone ($patients);

        $fc_list = Facility::whereIn('id', $facs->groupBy('facility_id')->pluck('facility_id'))->select('id','name')->get();
        $pros = Proponent::whereIn('id', $proponent->groupBy('proponent_id')->pluck('proponent_id'))->select('id','proponent')->get();
        $users = User::whereIn('userid', $by->groupBy('created_by')->pluck('created_by'))->select('userid','lname', 'fname')->get();
        $brgy = Barangay::whereIn('id', $barangay->groupBy('barangay_id')->pluck('barangay_id'))->select('id','description')->get();
        $mncty = Muncity::whereIn('id', $muncity->groupBy('muncity_id')->pluck('muncity_id'))->select('id','description')->get();
        $prvnc = Province::whereIn('id', $province->groupBy('province_id')->pluck('province_id'))->select('id','description')->get();
        $on = $on->groupBy(DB::raw('DATE(created_at)'))->pluck(DB::raw('MAX(DATE(created_at))'));
        $all_pat = clone ($patients);
        $proponents_code = Proponent::groupBy('proponent')->select(DB::raw('MAX(proponent) as proponent'), DB::raw('MAX(proponent_code) as proponent_code'),DB::raw('MAX(id) as id') )->get();
        // return $patients->paginate(10);
        return view('maif.proponent_patient', [
            'patients' => $patients->whereNotNull('sent_type')->paginate(50),
            'keyword' => $request->keyword,
            'provinces' => Province::get(),
            'municipalities' => Muncity::get(),
            'proponents' => $proponents_code,
            'barangays' => Barangay::get(),
            'facilities' => Facility::get(),
            'user' => Auth::user(),
            'date' =>  $date->groupBy('date_guarantee_letter')->pluck('date_guarantee_letter'),
            'fname' => $fname->groupBy('fname')->pluck('fname'),
            'mname' => $mname->groupBy('mname')->pluck('mname'),
            'lname' => $lname->groupBy('lname')->pluck('lname'),
            'fc_list' => $fc_list,
            'pros' => $pros,
            'code' => $code->groupBy('patient_code')->pluck('patient_code'),
            'region' => $region->groupBy('region')->pluck('region'),
            'pro1' => $province->groupBy('other_province')->pluck('other_province'),
            'prvnc' => $prvnc,
            'muncity' => $province->groupBy('other_muncity')->pluck('other_muncity'),
            'mncty' => $mncty,
            'barangay' => $barangay->groupBy('other_barangay')->pluck('other_barangay'),
            'brgy' => $brgy,
            'on' => $on,
            'by' => $users,
            'filter_date' => explode(',',$request->filter_date),
            'filter_fname' => explode(',',$request->filter_fname),
            'filter_mname' => explode(',',$request->filter_mname),
            'filter_lname' => explode(',',$request->filter_lname),
            'filter_facility' => explode(',',$request->filter_facility),
            'filter_proponent' => explode(',',$request->filter_proponent),
            'filter_code' => explode(',',$request->filter_code),
            'filter_region' => explode(',',$request->filter_region),
            'filter_province' => explode(',',$request->filter_province),
            'filter_municipality' => explode(',',$request->filter_municipality),
            'filter_barangay' => explode(',',$request->filter_barangay),
            'filter_on' => explode(',',$request->filter_on),
            'filter_by' => explode(',',$request->filter_by),
            'generate_dates' => $filter_date,
            'gen' => $request->gen,
            'order' => $order,
            'id_pat' => '',
            'onhold_facs' => AddFacilityInfo::where('sent_status', 1)->pluck('facility_id')->toArray()
        ]);
    }

    public function getAcronym($str) {
        $words = explode(' ', $str); 
        $acronym = '';
        
        foreach ($words as $word) {
            $acronym .= strtoupper(substr($word, 0, 1)); 
        }
        
        return $acronym;
    }

    public function changeProponent(Request $req){
        $user = Auth::user();
        $ids = array_map('intval', explode(',', $req->ids));
        $id = $req->id;

        foreach($ids as $item){
            $proponent = Proponent::where('id', $id)->first();
            $patient = Patients::where('id', $item)->first();
            $facility = Facility::where('id', $patient->id)->select('id', 'name')->first();

            $patientLogs = new PatientLogs();
            $patientLogs->patient_id = $patient->id;
            $patientLogs->fill(Arr::except($patient->toArray(), ['status', 'sent_type', 'user_type', 'transd_id', 'fc_status', 'expired', 'pro_used', 'rtrv_remarks']));
            unset($patientLogs->id);
            $patientLogs->save();
            $patientLogs->update([
                'transfer_from' => $id,
                'pat_rem' => $req->trans_rem
            ]);

            do {
                $random = rand(10, 99); 
                $patient_code = $proponent->proponent_code . '-' . 
                    $this->getAcronym($patient->facility->name) . 
                    date('YmdHis') . 
                    $user->id . 
                    $random;
                $check_code = Patients::where('patient_code', $patient_code)->first();
            } while ($check_code);
            
            $patient->update([
                'proponent_id' => $id,
                'patient_code' => $patient_code,
                'created_by' => $user->userid,
                'transfer_from' => $id
            ]);
        }
        return redirect()->back()->with('patient_transfer', true);
    }
}
