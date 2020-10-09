<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Http\Requests;
use App\IncentiveParamModel;
use App\PostingIncentiveModel;
use App\PortionOfBlockModel;
use App\PortionOfSliceModel;
use DB;

class PostedInsentifController extends Controller
{
    
    public function daftarKaryawan(){

        $incentiveparamlast = IncentiveParamModel::select('INCENTIVE_PARAM_ID','INCENTIVE_PARAM_YEAR','INCENTIVE_PARAM_MONTH',
                                                        DB::raw('CASE 
                                                                WHEN INCENTIVE_PARAM_MONTH = 1 THEN "Januari"
                                                                WHEN INCENTIVE_PARAM_MONTH = 2 THEN "Februari"
                                                                WHEN INCENTIVE_PARAM_MONTH = 3 THEN "Maret"
                                                                WHEN INCENTIVE_PARAM_MONTH = 4 THEN "April"
                                                                WHEN INCENTIVE_PARAM_MONTH = 5 THEN "Mei"
                                                                WHEN INCENTIVE_PARAM_MONTH = 6 THEN "Juni"
                                                                WHEN INCENTIVE_PARAM_MONTH = 7 THEN "Juli"
                                                                WHEN INCENTIVE_PARAM_MONTH = 8 THEN "Agustus"
                                                                WHEN INCENTIVE_PARAM_MONTH = 9 THEN "September"
                                                                WHEN INCENTIVE_PARAM_MONTH = 10 THEN "Oktober"
                                                                WHEN INCENTIVE_PARAM_MONTH = 11 THEN "Nopember"
                                                                WHEN INCENTIVE_PARAM_MONTH = 12 THEN "Desember"
                                                           END as INCENTIVE_PARAM_MONTH_NAME'))
                                                        ->where('INCENTIVE_PARAM_STATUS','6')
                                                        ->orderby('INCENTIVE_PARAM_ID','DESC')
                                                        ->first();

        $param = $incentiveparamlast->INCENTIVE_PARAM_ID;
        $bulan = $incentiveparamlast->INCENTIVE_PARAM_MONTH_NAME;
        $month = $incentiveparamlast->INCENTIVE_PARAM_MONTH;
        $tahun = $incentiveparamlast->INCENTIVE_PARAM_YEAR;

        $showPosting = PostingIncentiveModel::select(
                                                    'tx_posting_incentive.POSTING_INCENTIVE_NIK','tx_posting_incentive.POSTING_INCENTIVE_NAME','tm_position.POSITION_NAME',
                                                    'tm_group.GROUP_NAME','tr_shift.SHIFT_NAME','tr_block.BLOCK_NAME','tm_division.DIVISION_NAME','tr_slice.SLICE_NAME',
                                                    'tx_posting_incentive.POSTING_INCENTIVE_BLOCK', 'tx_posting_incentive.POSTING_INCENTIVE_ID',
                                                     DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_FLAT),0,"de_DE")) as POSTING_INCENTIVE_FLAT'),
                                                      DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_BOX),0,"de_DE")) as POSTING_INCENTIVE_BOX'),
                                                       DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_OJEKER),0,"de_DE")) as POSTING_INCENTIVE_OJEKER'),
                                                        DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_THRESHOLD),0,"de_DE")) as POSTING_INCENTIVE_THRESHOLD'),
                                                         DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_FINAL),0,"de_DE")) as POSTING_INCENTIVE_FINAL'),
                                                         'tx_box_adjusment_emp.BAE_BOX_HANDLE',
                                                         'tx_posting_incentive.POSTING_INCENTIVE_PERCENT_CUT',
                                                         DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_COST_CUT),0,"de_DE")) as POSTING_INCENTIVE_COST_CUT'),
                                                         DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_DIST_CUT),0,"de_DE")) as POSTING_INCENTIVE_DIST_CUT'),
                                                         DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_FINAL_AFTER_CUT),0,"de_DE")) as POSTING_INCENTIVE_FINAL_AFTER_CUT')
                                                    )
                                            ->leftjoin('tx_box_adjusment_emp',function($join) use ($param){
                                                $join->on('tx_posting_incentive.POSTING_INCENTIVE_NIK','=','tx_box_adjusment_emp.EMPLOYEE_NIK')
                                                     ->where('tx_box_adjusment_emp.INCENTIVE_PARAM_ID','=',$param);
                                            })
                                            ->leftjoin('tm_position','tm_position.POSITION_ID','=','tx_posting_incentive.POSTING_INCENTIVE_POS')
                                            ->leftjoin('tm_group','tm_group.GROUP_ID','=','tx_posting_incentive.POSTING_INCENTIVE_GROUP')
                                            ->leftjoin('tr_shift','tr_shift.SHIFT_ID','=','tx_posting_incentive.POSTING_INCENTIVE_SHIFT')
                                            ->leftjoin('tr_block','tr_block.BLOCK_ID','=','tx_posting_incentive.POSTING_INCENTIVE_BLOCK')
                                            ->leftjoin('tm_division','tm_division.DIVISION_ID','=','tx_posting_incentive.POSTING_INCENTIVE_DIV')
                                            ->leftjoin('tr_slice','tr_slice.SLICE_ID','=','POSTING_INCENTIVE_SLICE')
                                            ->where('tx_posting_incentive.INCENTIVE_PARAM_ID',$param)
                                            // ->whereRaw("tx_posting_incentive.POSTING_INCENTIVE_NIK IN ('K980424', 'K980368', 'K010568', 'K010570', 'K970076', 'K970084', 'K980456', 'K970153', 'K070602', 'K970056', 'K970151', 'K980340')")
                                            ->get();

        $showPortionOfSlice = PortionOfSliceModel::select(
                                                           'BLOCK_ID','SLICE_ID','POS_DIFF_PER_SLICE','POS_HEADACOUNT_PER_SLICE','POS_COEFFICIENT_UPPER_VALUE',
                                                           'POS_BOBOT','POS_PERSENTASE_SLICE','POS_PERSENTASE_SLICE_PER_EMP',
                                                           DB::raw('CONCAT("Rp. ", FORMAT(POS_KUE_PER_SLICE,0,"de_DE")) as POS_KUE_PER_SLICE'),
                                                           DB::raw('CONCAT("Rp. ", FORMAT(POS_NOM_FLAT_DIST_BLOCK_I,0,"de_DE")) as POS_NOM_FLAT_DIST_BLOCK_I')
                                                         )
                                                ->where('INCENTIVE_PARAM_ID',$param)
                                                ->get();

        $showPortionOfBlock = PortionOfBlockModel::select('*',DB::raw('ROUND(POB_BLOCK_COMPOSITION,2) as Block_Composition'))
                                                    ->where('INCENTIVE_PARAM_ID',$param)
                                                    ->get();

        return Response::json(array('showPosting' => $showPosting, 'showPortionOfSlice' => $showPortionOfSlice, 'showPortionOfBlock' => $showPortionOfBlock, 'bulan' => $bulan, 'tahun' => $tahun, 'month' => $month, 'param' => $param));

    }

    public function searchNameInsentif($search){
        $dicari = explode('&', $search);
        $search = $dicari[0];
        $bulan = $dicari[1];
        $tahun = $dicari[2];

        if ($search == 'uhuy') {
            $search = '';
        }

        $incentiveparamlast = IncentiveParamModel::select('INCENTIVE_PARAM_ID','INCENTIVE_PARAM_YEAR','INCENTIVE_PARAM_MONTH',
                                                        DB::raw('CASE 
                                                                WHEN INCENTIVE_PARAM_MONTH = 1 THEN "Januari"
                                                                WHEN INCENTIVE_PARAM_MONTH = 2 THEN "Februari"
                                                                WHEN INCENTIVE_PARAM_MONTH = 3 THEN "Maret"
                                                                WHEN INCENTIVE_PARAM_MONTH = 4 THEN "April"
                                                                WHEN INCENTIVE_PARAM_MONTH = 5 THEN "Mei"
                                                                WHEN INCENTIVE_PARAM_MONTH = 6 THEN "Juni"
                                                                WHEN INCENTIVE_PARAM_MONTH = 7 THEN "Juli"
                                                                WHEN INCENTIVE_PARAM_MONTH = 8 THEN "Agustus"
                                                                WHEN INCENTIVE_PARAM_MONTH = 9 THEN "September"
                                                                WHEN INCENTIVE_PARAM_MONTH = 10 THEN "Oktober"
                                                                WHEN INCENTIVE_PARAM_MONTH = 11 THEN "Nopember"
                                                                WHEN INCENTIVE_PARAM_MONTH = 12 THEN "Desember"
                                                           END as INCENTIVE_PARAM_MONTH_NAME'))
                                                        ->where('INCENTIVE_PARAM_STATUS','6')
                                                        ->where('INCENTIVE_PARAM_MONTH',$bulan)
                                                        ->where('INCENTIVE_PARAM_YEAR',$tahun)
                                                        ->first();

        $param = $incentiveparamlast->INCENTIVE_PARAM_ID;
        $bulan = $incentiveparamlast->INCENTIVE_PARAM_MONTH_NAME;
        $month = $incentiveparamlast->INCENTIVE_PARAM_MONTH;
        $tahun = $incentiveparamlast->INCENTIVE_PARAM_YEAR;

        $showPosting = PostingIncentiveModel::select(
                                                    'tx_posting_incentive.POSTING_INCENTIVE_NIK','tx_posting_incentive.POSTING_INCENTIVE_NAME','tm_position.POSITION_NAME',
                                                    'tm_group.GROUP_NAME','tr_shift.SHIFT_NAME','tr_block.BLOCK_NAME','tm_division.DIVISION_NAME','tr_slice.SLICE_NAME',
                                                    'tx_posting_incentive.POSTING_INCENTIVE_BLOCK',
                                                     DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_FLAT),0,"de_DE")) as POSTING_INCENTIVE_FLAT'),
                                                      DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_BOX),0,"de_DE")) as POSTING_INCENTIVE_BOX'),
                                                       DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_OJEKER),0,"de_DE")) as POSTING_INCENTIVE_OJEKER'),
                                                        DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_THRESHOLD),0,"de_DE")) as POSTING_INCENTIVE_THRESHOLD'),
                                                         DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_FINAL),0,"de_DE")) as POSTING_INCENTIVE_FINAL'),
                                                         'tx_box_adjusment_emp.BAE_BOX_HANDLE',
                                                         'tx_posting_incentive.POSTING_INCENTIVE_PERCENT_CUT',
                                                         DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_COST_CUT),0,"de_DE")) as POSTING_INCENTIVE_COST_CUT'),
                                                         DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_DIST_CUT),0,"de_DE")) as POSTING_INCENTIVE_DIST_CUT'),
                                                         DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_FINAL_AFTER_CUT),0,"de_DE")) as POSTING_INCENTIVE_FINAL_AFTER_CUT')
                                                    )
                                            ->leftjoin('tx_box_adjusment_emp',function($join) use ($param){
                                                $join->on('tx_posting_incentive.POSTING_INCENTIVE_NIK','=','tx_box_adjusment_emp.EMPLOYEE_NIK')
                                                     ->where('tx_box_adjusment_emp.INCENTIVE_PARAM_ID','=',$param);
                                            })
                                            ->leftjoin('tm_position','tm_position.POSITION_ID','=','tx_posting_incentive.POSTING_INCENTIVE_POS')
                                            ->leftjoin('tm_group','tm_group.GROUP_ID','=','tx_posting_incentive.POSTING_INCENTIVE_GROUP')
                                            ->leftjoin('tr_shift','tr_shift.SHIFT_ID','=','tx_posting_incentive.POSTING_INCENTIVE_SHIFT')
                                            ->leftjoin('tr_block','tr_block.BLOCK_ID','=','tx_posting_incentive.POSTING_INCENTIVE_BLOCK')
                                            ->leftjoin('tm_division','tm_division.DIVISION_ID','=','tx_posting_incentive.POSTING_INCENTIVE_DIV')
                                            ->leftjoin('tr_slice','tr_slice.SLICE_ID','=','POSTING_INCENTIVE_SLICE')
                                            ->where(function($q) use ($search){
                                                $q->where('POSTING_INCENTIVE_NAME','like','%'.$search.'%')
                                                    ->orWhere('tm_position.POSITION_NAME','like','%'.$search.'%')->orWhere('tx_posting_incentive.POSTING_INCENTIVE_NIK','like','%'.$search.'%')
                                                    ->orWhere('tr_block.BLOCK_NAME','like','%'.$search.'%')->orWhere('tr_slice.SLICE_NAME','like','%'.$search.'%');
                                            })
                                            ->where('tx_posting_incentive.INCENTIVE_PARAM_ID',$param)
                                            // ->whereRaw("tx_posting_incentive.POSTING_INCENTIVE_NIK IN ('K980424', 'K980368', 'K010568', 'K010570', 'K970076', 'K970084', 'K980456', 'K970153', 'K070602', 'K970056', 'K970151', 'K980340')")
                                            ->get();

        $showPortionOfSlice = PortionOfSliceModel::select(
                                                           'BLOCK_ID','SLICE_ID','POS_DIFF_PER_SLICE','POS_HEADACOUNT_PER_SLICE','POS_COEFFICIENT_UPPER_VALUE',
                                                           'POS_BOBOT','POS_PERSENTASE_SLICE','POS_PERSENTASE_SLICE_PER_EMP',
                                                           DB::raw('CONCAT("Rp. ", FORMAT(POS_KUE_PER_SLICE,0,"de_DE")) as POS_KUE_PER_SLICE'),
                                                           DB::raw('CONCAT("Rp. ", FORMAT(POS_NOM_FLAT_DIST_BLOCK_I,0,"de_DE")) as POS_NOM_FLAT_DIST_BLOCK_I')
                                                         )
                                                ->where('INCENTIVE_PARAM_ID',$param)
                                                ->get();

        $showPortionOfBlock = PortionOfBlockModel::select('*',
                                                          DB::raw('(POB_DIFF_PER_BLOCK*100) as POB_DIFF_PER_BLOCK_FIX'), DB::raw('ROUND(POB_BLOCK_COMPOSITION,2) as Block_Composition'))
                                                    ->where('INCENTIVE_PARAM_ID',$param)
                                                    ->get();
        return Response::json(array('showPosting' => $showPosting, 'showPortionOfSlice' => $showPortionOfSlice, 'showPortionOfBlock' => $showPortionOfBlock, 'bulan' => $bulan, 'tahun' => $tahun));                                           
    }

    public function SearchBulanTahun($id){

        $dicari = explode('&', $id);
        $bulan = $dicari[0];
        $tahun = $dicari[1];

        $count = IncentiveParamModel::select('*')->where('INCENTIVE_PARAM_MONTH',$bulan)->where('INCENTIVE_PARAM_YEAR',$tahun)->count();
        if ($count > 0) {
            $incentiveparamlast = IncentiveParamModel::select('INCENTIVE_PARAM_ID','INCENTIVE_PARAM_YEAR','INCENTIVE_PARAM_MONTH',
                                                        DB::raw('CASE 
                                                                WHEN INCENTIVE_PARAM_MONTH = 1 THEN "Januari"
                                                                WHEN INCENTIVE_PARAM_MONTH = 2 THEN "Februari"
                                                                WHEN INCENTIVE_PARAM_MONTH = 3 THEN "Maret"
                                                                WHEN INCENTIVE_PARAM_MONTH = 4 THEN "April"
                                                                WHEN INCENTIVE_PARAM_MONTH = 5 THEN "Mei"
                                                                WHEN INCENTIVE_PARAM_MONTH = 6 THEN "Juni"
                                                                WHEN INCENTIVE_PARAM_MONTH = 7 THEN "Juli"
                                                                WHEN INCENTIVE_PARAM_MONTH = 8 THEN "Agustus"
                                                                WHEN INCENTIVE_PARAM_MONTH = 9 THEN "September"
                                                                WHEN INCENTIVE_PARAM_MONTH = 10 THEN "Oktober"
                                                                WHEN INCENTIVE_PARAM_MONTH = 11 THEN "Nopember"
                                                                WHEN INCENTIVE_PARAM_MONTH = 12 THEN "Desember"
                                                           END as INCENTIVE_PARAM_MONTH_NAME'))
                                                        ->where('INCENTIVE_PARAM_STATUS','6')                                                        
                                                        ->where('INCENTIVE_PARAM_MONTH',$bulan)
                                                        ->where('INCENTIVE_PARAM_YEAR',$tahun)
                                                        ->first();

            $param = $incentiveparamlast->INCENTIVE_PARAM_ID;
            $bulan = $incentiveparamlast->INCENTIVE_PARAM_MONTH_NAME;
            $month = $incentiveparamlast->INCENTIVE_PARAM_MONTH;
            $tahun = $incentiveparamlast->INCENTIVE_PARAM_YEAR;

            $showPosting = PostingIncentiveModel::select(
                                                        'tx_posting_incentive.POSTING_INCENTIVE_NIK','tx_posting_incentive.POSTING_INCENTIVE_NAME','tm_position.POSITION_NAME',
                                                        'tm_group.GROUP_NAME','tr_shift.SHIFT_NAME','tr_block.BLOCK_NAME','tm_division.DIVISION_NAME','tr_slice.SLICE_NAME',
                                                        'tx_posting_incentive.POSTING_INCENTIVE_BLOCK',
                                                         DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_FLAT),0,"de_DE")) as POSTING_INCENTIVE_FLAT'),
                                                          DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_BOX),0,"de_DE")) as POSTING_INCENTIVE_BOX'),
                                                           DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_OJEKER),0,"de_DE")) as POSTING_INCENTIVE_OJEKER'),
                                                            DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_THRESHOLD),0,"de_DE")) as POSTING_INCENTIVE_THRESHOLD'),
                                                             DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_FINAL),0,"de_DE")) as POSTING_INCENTIVE_FINAL'),
                                                             'tx_box_adjusment_emp.BAE_BOX_HANDLE',
                                                             'tx_posting_incentive.POSTING_INCENTIVE_PERCENT_CUT',
                                                         DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_COST_CUT),0,"de_DE")) as POSTING_INCENTIVE_COST_CUT'),
                                                         DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_DIST_CUT),0,"de_DE")) as POSTING_INCENTIVE_DIST_CUT'),
                                                         DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_FINAL_AFTER_CUT),0,"de_DE")) as POSTING_INCENTIVE_FINAL_AFTER_CUT')
                                                        )
                                                ->leftjoin('tx_box_adjusment_emp',function($join) use ($param){
                                                    $join->on('tx_posting_incentive.POSTING_INCENTIVE_NIK','=','tx_box_adjusment_emp.EMPLOYEE_NIK')
                                                         ->where('tx_box_adjusment_emp.INCENTIVE_PARAM_ID','=',$param);
                                                })
                                                ->leftjoin('tm_position','tm_position.POSITION_ID','=','tx_posting_incentive.POSTING_INCENTIVE_POS')
                                                ->leftjoin('tm_group','tm_group.GROUP_ID','=','tx_posting_incentive.POSTING_INCENTIVE_GROUP')
                                                ->leftjoin('tr_shift','tr_shift.SHIFT_ID','=','tx_posting_incentive.POSTING_INCENTIVE_SHIFT')
                                                ->leftjoin('tr_block','tr_block.BLOCK_ID','=','tx_posting_incentive.POSTING_INCENTIVE_BLOCK')
                                                ->leftjoin('tm_division','tm_division.DIVISION_ID','=','tx_posting_incentive.POSTING_INCENTIVE_DIV')
                                                ->leftjoin('tr_slice','tr_slice.SLICE_ID','=','POSTING_INCENTIVE_SLICE')
                                                ->where('tx_posting_incentive.INCENTIVE_PARAM_ID',$param)
                                                // ->whereRaw("tx_posting_incentive.POSTING_INCENTIVE_NIK IN ('K980424', 'K980368', 'K010568', 'K010570', 'K970076', 'K970084', 'K980456', 'K970153', 'K070602', 'K970056', 'K970151', 'K980340')")
                                                ->get();

            $showPortionOfSlice = PortionOfSliceModel::select(
                                                               'BLOCK_ID','SLICE_ID','POS_DIFF_PER_SLICE','POS_HEADACOUNT_PER_SLICE','POS_COEFFICIENT_UPPER_VALUE',
                                                               'POS_BOBOT','POS_PERSENTASE_SLICE','POS_PERSENTASE_SLICE_PER_EMP',
                                                               DB::raw('CONCAT("Rp. ", FORMAT(POS_KUE_PER_SLICE,0,"de_DE")) as POS_KUE_PER_SLICE'),
                                                               DB::raw('CONCAT("Rp. ", FORMAT(POS_NOM_FLAT_DIST_BLOCK_I,0,"de_DE")) as POS_NOM_FLAT_DIST_BLOCK_I')
                                                             )
                                                    ->where('INCENTIVE_PARAM_ID',$param)
                                                    ->get();

            $showPortionOfBlock = PortionOfBlockModel::select('*', DB::raw('ROUND(POB_BLOCK_COMPOSITION,2) as Block_Composition'))
                                                        ->where('INCENTIVE_PARAM_ID',$param)
                                                        ->get();

            return Response::json(array('success' => true, 'showPosting' => $showPosting, 'showPortionOfSlice' => $showPortionOfSlice, 'showPortionOfBlock' => $showPortionOfBlock, 'bulan' => $bulan, 'tahun' => $tahun, 'month' => $month, 'param' => $param));
        } else {
            return Response::json(array('success' => false));
        }

    }

}
