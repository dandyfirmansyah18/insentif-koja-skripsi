<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Response;
use App\IncentiveParamModel;
use App\PortionOfBlockModel;
use App\PortionOfSliceModel;
use App\AdjustParamModel;
use App\BoxAdjusmentEmpModel;
use App\BoxAdjusmentGroupModel;
use App\TempInsentifModel;
use App\PostingIncentiveModel;
use App\EmployeeModel;
use App\Http\Requests;
use Input;
use Excel;
use File;
use Barryvdh\DomPDF\Facade as PDF;

class InsentifController extends Controller
{
    public function cekSetting(){
        $cekSetting = AdjustParamModel::select('*')->where('ADJUST_PARAM_ID',1)->first();
        $AP_PERCENTASE_INCENTIVE = $cekSetting->AP_PERCENTASE_INCENTIVE;
        $AP_POB_DIFF_BLOCK_I = $cekSetting->AP_POB_DIFF_BLOCK_I;
        $AP_POB_DIFF_BLOCK_II = $cekSetting->AP_POB_DIFF_BLOCK_II;
        $AP_POB_DIFF_BLOCK_III = $cekSetting->AP_POB_DIFF_BLOCK_III;
        $AP_POB_CUV_BLOCK_I = $cekSetting->AP_POB_CUV_BLOCK_I;
        $AP_POS_DIST_FLAT_BLOCK_I = $cekSetting->AP_POS_DIST_FLAT_BLOCK_I;
        $AP_POS_DIFF_SLICE_BLOCK_I = $cekSetting->AP_POS_DIFF_SLICE_BLOCK_I;
        $AP_POS_DIFF_SLICE_BLOCK_IIA = $cekSetting->AP_POS_DIFF_SLICE_BLOCK_IIA;
        $AP_POS_DIFF_SLICE_BLOCK_IIB = $cekSetting->AP_POS_DIFF_SLICE_BLOCK_IIB;
        $AP_POS_DIFF_SLICE_BLOCK_III = $cekSetting->AP_POS_DIFF_SLICE_BLOCK_III;
        $AP_POS_CUV_BLOCK_I = $cekSetting->AP_POS_CUV_BLOCK_I;
        $AP_POS_CUV_BLOCK_II = $cekSetting->AP_POS_CUV_BLOCK_II;
        $AP_POS_CUV_BLOCK_III = $cekSetting->AP_POS_CUV_BLOCK_III;
        $AP_PENALTY = $cekSetting->AP_PENALTY;
        $AP_BA_MIN_BOX_QCC = $cekSetting->AP_BA_MIN_BOX_QCC;
        $AP_BA_MIN_BOX_RTG = $cekSetting->AP_BA_MIN_BOX_RTG;

        if (
            $AP_PERCENTASE_INCENTIVE === null or
            $AP_POB_DIFF_BLOCK_I === null or
            $AP_POB_DIFF_BLOCK_II === null or
            $AP_POB_DIFF_BLOCK_III === null or
            $AP_POB_CUV_BLOCK_I === null or
            $AP_POS_DIST_FLAT_BLOCK_I === null or
            $AP_POS_DIFF_SLICE_BLOCK_I === null or
            $AP_POS_DIFF_SLICE_BLOCK_IIA === null or
            $AP_POS_DIFF_SLICE_BLOCK_IIB === null or
            $AP_POS_DIFF_SLICE_BLOCK_III === null or
            $AP_POS_CUV_BLOCK_I === null or
            $AP_POS_CUV_BLOCK_II === null or
            $AP_POS_CUV_BLOCK_III === null or
            $AP_PENALTY === null or
            $AP_BA_MIN_BOX_QCC === null or
            $AP_BA_MIN_BOX_RTG === null
            ) {
            $ceking = 'salah';
        } else {
            $ceking = 'benar';
        }

        return Response::json(array('success' => $ceking, 'param' => $AP_PERCENTASE_INCENTIVE));
    }

    public function checkStep2($incentiveparam)
    {
        $countStep2 = BoxAdjusmentGroupModel::where('INCENTIVE_PARAM_ID',$incentiveparam)->count();
        return Response::json($countStep2);
    }

    public function cekStep(){
        $cek = IncentiveParamModel::select('*')->where('INCENTIVE_PARAM_STATUS','<',6)->count();
        if ($cek > 0) {
            $datas = IncentiveParamModel::select('INCENTIVE_PARAM_STATUS','INCENTIVE_PARAM_ID')->where('INCENTIVE_PARAM_STATUS','<',6)->orderBy('INCENTIVE_PARAM_ID','DESC')->first();
            $status = $datas->INCENTIVE_PARAM_STATUS;
            $incentiveparam = $datas->INCENTIVE_PARAM_ID;
            return Response::json(array('status_last' => $status,'incentiveparam' => $incentiveparam));
        } else {
            return Response::json(array('status_last' => 9));
        }
    }

    public function showStep1($incentiveparam){
        $data = IncentiveParamModel::select('INCENTIVE_PARAM_ID as incentiveparam','INCENTIVE_PARAM_MONTH as bulan','INCENTIVE_PARAM_YEAR as tahun',
                                            DB::raw('CONCAT("Rp. ", FORMAT(INCENTIVE_PARAM_INCOME,0,"de_DE")) as penghasilan'),
                                            DB::raw('CONCAT("Rp. ", FORMAT(INCENTIVE_PARAM_VALUTA,0,"de_DE")) as valuta'),                                            
                                            'INCENTIVE_PARAM_DIST as param', 'INCENTIVE_PARAM_VALUE as total_insentif'
                                            )
                                    ->where('INCENTIVE_PARAM_ID',$incentiveparam)
                                    ->first();
        return Response::json($data);
    }

    public function showStep2($incentiveparam){
        $data = DB::table('tx_box_adjusment_group')->select('BOX_ADJUSMENT_GROUP_ID','BOX_ADJUSMENT_GROUP_VALUE')->where('INCENTIVE_PARAM_ID',$incentiveparam)->get();
        return Response::json($data);       
    }

    public function showStep3($incentiveparam){

        // $minmaster = DB::table('tx_incentive_param')
        //                 ->select('INCENTIVE_PARAM_MIN_BOX_RTG','INCENTIVE_PARAM_MIN_BOX_QCC')
        //                 ->where('INCENTIVE_PARAM_ID',$incentiveparam)
        //                 ->first();

        $minmaster = DB::table('tr_adjust_param')
                        ->select('AP_BA_MIN_BOX_RTG','AP_BA_MIN_BOX_QCC')
                        ->first();

        $minboxrtg = $minmaster->AP_BA_MIN_BOX_RTG;
        $minboxqcc = $minmaster->AP_BA_MIN_BOX_QCC;

        $incentivesliceqcc = DB::table('tx_portion_of_slice')
                                ->select('POS_KUE_PER_SLICE','POS_NOM_FLAT_DIST_BLOCK_I')
                                ->where('BLOCK_ID',1)->where('SLICE_ID',1)
                                ->where('INCENTIVE_PARAM_ID',$incentiveparam)
                                ->first();                                
        $incentivesliceqccs = $incentivesliceqcc->POS_KUE_PER_SLICE - $incentivesliceqcc->POS_NOM_FLAT_DIST_BLOCK_I;


        $incentiveslicertg = DB::table('tx_portion_of_slice')
                                ->select('POS_KUE_PER_SLICE','POS_NOM_FLAT_DIST_BLOCK_I')
                                ->where('BLOCK_ID',1)
                                ->where('SLICE_ID',2)
                                ->where('INCENTIVE_PARAM_ID',$incentiveparam)
                                ->first();                                
        $incentiveslicertgs = $incentiveslicertg->POS_KUE_PER_SLICE - $incentiveslicertg->POS_NOM_FLAT_DIST_BLOCK_I; 

        $selectsumboxorang = BoxAdjusmentGroupModel::select('tx_box_adjusment_group.BOX_ADJUSMENT_GROUP_ID','tx_box_adjusment_group.BOX_ADJUSMENT_GROUP_VALUE',
                                                     'tx_box_adjusment_group.BOX_ADJUSMENT_GROUP_INCENTIVE', 'tx_box_adjusment_group.BOX_ADJUSMENT_GROUP_PRICE',
                                                    DB::raw('CASE WHEN SUM(tx_box_adjusment_emp.BAE_BOX_HANDLE) IS NULL THEN 0 ELSE SUM(tx_box_adjusment_emp.BAE_BOX_HANDLE) END AS sum'))
                                                    ->leftjoin('tx_box_adjusment_emp','tx_box_adjusment_emp.BOX_ADJUSMENT_GROUP_ID','=','tx_box_adjusment_group.BOX_ADJUSMENT_GROUP_ID')
                                                    ->where('tx_box_adjusment_group.INCENTIVE_PARAM_ID',$incentiveparam)
                                                    ->groupby('tx_box_adjusment_group.BOX_ADJUSMENT_GROUP_ID','tx_box_adjusment_group.BOX_ADJUSMENT_GROUP_VALUE','tx_box_adjusment_group.BOX_ADJUSMENT_GROUP_INCENTIVE', 'tx_box_adjusment_group.BOX_ADJUSMENT_GROUP_PRICE')
                                                    ->get();

        $incentiveGroupQCCA = $selectsumboxorang[0]->BOX_ADJUSMENT_GROUP_INCENTIVE;
        $incentiveGroupQCCB = $selectsumboxorang[1]->BOX_ADJUSMENT_GROUP_INCENTIVE;
        $incentiveGroupQCCC = $selectsumboxorang[2]->BOX_ADJUSMENT_GROUP_INCENTIVE;
        $incentiveGroupQCCD = $selectsumboxorang[3]->BOX_ADJUSMENT_GROUP_INCENTIVE;

        $incentiveGroupRTGA = $selectsumboxorang[4]->BOX_ADJUSMENT_GROUP_INCENTIVE;
        $incentiveGroupRTGB = $selectsumboxorang[5]->BOX_ADJUSMENT_GROUP_INCENTIVE;
        $incentiveGroupRTGC = $selectsumboxorang[6]->BOX_ADJUSMENT_GROUP_INCENTIVE;
        $incentiveGroupRTGD = $selectsumboxorang[7]->BOX_ADJUSMENT_GROUP_INCENTIVE;

        $hargaincentiveQCCA = $selectsumboxorang[0]->BOX_ADJUSMENT_GROUP_PRICE;
        $hargaincentiveQCCB = $selectsumboxorang[1]->BOX_ADJUSMENT_GROUP_PRICE;
        $hargaincentiveQCCC = $selectsumboxorang[2]->BOX_ADJUSMENT_GROUP_PRICE;
        $hargaincentiveQCCD = $selectsumboxorang[3]->BOX_ADJUSMENT_GROUP_PRICE;

        $hargaincentiveRTGA = $selectsumboxorang[4]->BOX_ADJUSMENT_GROUP_PRICE;
        $hargaincentiveRTGB = $selectsumboxorang[5]->BOX_ADJUSMENT_GROUP_PRICE;
        $hargaincentiveRTGC = $selectsumboxorang[6]->BOX_ADJUSMENT_GROUP_PRICE;
        $hargaincentiveRTGD = $selectsumboxorang[7]->BOX_ADJUSMENT_GROUP_PRICE;

        $sumboxorangqcca = $selectsumboxorang[0]->sum;
        $sumboxorangqccb = $selectsumboxorang[1]->sum;
        $sumboxorangqccc = $selectsumboxorang[2]->sum;
        $sumboxorangqccd = $selectsumboxorang[3]->sum;

        $sumboxorangrtga = $selectsumboxorang[4]->sum;
        $sumboxorangrtgb = $selectsumboxorang[5]->sum;
        $sumboxorangrtgc = $selectsumboxorang[6]->sum;
        $sumboxorangrtgd = $selectsumboxorang[7]->sum;

        $overboxqcca = $selectsumboxorang[0]->BOX_ADJUSMENT_GROUP_VALUE - $selectsumboxorang[0]->sum;
        $overboxqccb = $selectsumboxorang[1]->BOX_ADJUSMENT_GROUP_VALUE - $selectsumboxorang[1]->sum;
        $overboxqccc = $selectsumboxorang[2]->BOX_ADJUSMENT_GROUP_VALUE - $selectsumboxorang[2]->sum;
        $overboxqccd = $selectsumboxorang[3]->BOX_ADJUSMENT_GROUP_VALUE - $selectsumboxorang[3]->sum;

        $overboxrtga = $selectsumboxorang[4]->BOX_ADJUSMENT_GROUP_VALUE - $selectsumboxorang[4]->sum;
        $overboxrtgb = $selectsumboxorang[5]->BOX_ADJUSMENT_GROUP_VALUE - $selectsumboxorang[5]->sum;
        $overboxrtgc = $selectsumboxorang[6]->BOX_ADJUSMENT_GROUP_VALUE - $selectsumboxorang[6]->sum;
        $overboxrtgd = $selectsumboxorang[7]->BOX_ADJUSMENT_GROUP_VALUE - $selectsumboxorang[7]->sum;

        // dd($overboxqcca,$overboxqccb,$overboxqccc,$overboxqccd,$overboxrtga,$overboxrtgb,$overboxrtgc,$overboxrtgd);

        return Response::json(array( 

                                    'BoxPerOrangA' => $sumboxorangqcca,
                                    'BoxPerOrangB' => $sumboxorangqccb,
                                    'BoxPerOrangC' => $sumboxorangqccc,
                                    'BoxPerOrangD' => $sumboxorangqccd,

                                    'RtgBoxPerOrangA' => $sumboxorangrtga,
                                    'RtgBoxPerOrangB' => $sumboxorangrtgb,
                                    'RtgBoxPerOrangC' => $sumboxorangrtgc,
                                    'RtgBoxPerOrangD' => $sumboxorangrtgd,

                                    'InsentifPerUnitA'=>$incentiveGroupQCCA,
                                    'InsentifPerUnitB'=>$incentiveGroupQCCB,
                                    'InsentifPerUnitC'=>$incentiveGroupQCCC,
                                    'InsentifPerUnitD'=>$incentiveGroupQCCD,
                                    'RtgInsentifPerUnitA'=>$incentiveGroupRTGA,
                                    'RtgInsentifPerUnitB'=>$incentiveGroupRTGB,
                                    'RtgInsentifPerUnitC'=>$incentiveGroupRTGC,
                                    'RtgInsentifPerUnitD'=>$incentiveGroupRTGD,

                                    'InsentifPerBoxA'=>$hargaincentiveQCCA,
                                    'InsentifPerBoxB'=>$hargaincentiveQCCB,
                                    'InsentifPerBoxC'=>$hargaincentiveQCCC,
                                    'InsentifPerBoxD'=>$hargaincentiveQCCD,
                                    'RtgInsentifPerBoxA'=>$hargaincentiveRTGA,
                                    'RtgInsentifPerBoxB'=>$hargaincentiveRTGB,
                                    'RtgInsentifPerBoxC'=>$hargaincentiveRTGC,
                                    'RtgInsentifPerBoxD'=>$hargaincentiveRTGD,

                                    'minboxrtg'=>$minboxrtg,
                                    'minboxqcc'=>$minboxqcc,
                                    'kueinsqcc'=>$incentivesliceqccs,
                                    'kueinsrtg'=>$incentiveslicertgs,

                                    'OverBoxA' => $overboxqcca,
                                    'OverBoxB' => $overboxqccb,
                                    'OverBoxC' => $overboxqccc,
                                    'OverBoxD' => $overboxqccd,

                                    'RtgOverBoxA' => $overboxrtga,
                                    'RtgOverBoxB' => $overboxrtgb,
                                    'RtgOverBoxC' => $overboxrtgc,
                                    'RtgOverBoxD' => $overboxrtgd,

                                    )
                            );


    }

    public function saveStep1(Request $request,$id){

        $countBlock1 = $request->get('getCountBlock1');
        $countBlock2 = $request->get('getCountBlock2');
        $countBlock3 = $request->get('getCountBlock3');

        $incentive_value = $request->get('total_insentif');

        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $param = (double)$id;

        $penghasilan = $request->get('penghasilan');
        $peng = substr($penghasilan,4);
        $pengs = explode(".",$peng);
        foreach ($pengs as $hasilan){
            $hasilan = wordwrap($hasilan);
        }
        $hasilans = join('', $pengs);
        $penghasilanfix = (float)$hasilans;

        // $qccfix = $request->get('qcc');

        // $rtgfix = $request->get('rtg');

        $valuta = $request->get('valuta');
        $val = substr($valuta,4);
        $vals = explode(".",$val);
        foreach ($vals as $valuta){
            $valuta = wordwrap($valuta);
        }
        $valutas = join('', $vals);
        $valutafix = (float)$valutas;

        $insentifbudget = $param/100 * $penghasilanfix;

        // return $insentifbudget;

        // return $insentifbudget;
        $settingmaster = DB::table('tr_adjust_param')
                            ->select('*')
                            ->first();


        $upperBlock1 = $settingmaster->AP_POB_CUV_BLOCK_I; // 
        $diffBlock1 = $settingmaster->AP_POB_DIFF_BLOCK_I;
        $diffBlock2 = $settingmaster->AP_POB_DIFF_BLOCK_II;
        $diffBlock3 = $settingmaster->AP_POB_DIFF_BLOCK_III;


        $diffBlock3 = $diffBlock3/100;
        $diffBlock2 = $diffBlock2/100;
        $upperBlock3 = $upperBlock1*(1-$diffBlock3);
        $upperBlock2 = $upperBlock3*(1-$diffBlock2);
        $bobot1 = $upperBlock1 * $countBlock1;
        $bobot2 = $upperBlock2 * $countBlock2;
        $bobot3 = $upperBlock3 * $countBlock3;
        $sumbobot = $bobot1 + $bobot2 + $bobot3;
        $persenBlock1 = $bobot1/$sumbobot * 100;
        $persenBlock2 = $bobot2/$sumbobot * 100;
        $persenBlock3 = $bobot3/$sumbobot * 100;
        $persenBlockKar1 = $persenBlock1/$countBlock1;
        $persenBlockKar2 = $persenBlock2/$countBlock2;
        $persenBlockKar3 = $persenBlock3/$countBlock3;
        $kueBlock1 = $persenBlock1*1/100;
        $kueBlock2 = $persenBlock2*1/100;
        $kueBlock3 = $persenBlock3*1/100;
        $avgBlock1 = $kueBlock1/$countBlock1;
        $avgBlock2 = $kueBlock2/$countBlock2;
        $avgBlock3 = $kueBlock3/$countBlock3;
        $diffNomBlock1 = 0;
        $diffNomBlock2 = $avgBlock3 - $avgBlock2;
        $diffNomBlock3 = $avgBlock1 - $avgBlock3;
        $diffPersenBlock1 = 0;
        $diffPersenBlock2 = $diffNomBlock2/$avgBlock3*100;
        $diffPersenBlock3 = $diffNomBlock3/$avgBlock1*100;
        $sumkueperblock = $kueBlock1 + $kueBlock2 + $kueBlock3;
        $BlockPerCom1 = $kueBlock1/$sumkueperblock*100;
        $BlockPerCom2 = $kueBlock2/$sumkueperblock*100;
        $BlockPerCom3 = $kueBlock3/$sumkueperblock*100;

        // return array($BlockPerCom1,
        //             $BlockPerCom2 ,
        //             $BlockPerCom3);

        if ($request->get('incentiveparam') == null or $request->get('incentiveparam') == "") {

            $cekExist = IncentiveParamModel::select('*')->where('INCENTIVE_PARAM_MONTH',$bulan)->where('INCENTIVE_PARAM_YEAR',$tahun)->count();
            if ($cekExist > 0) {
                return Response::json(array('success'=>false));
            }else{

                $boom = new IncentiveParamModel();
                $boom->INCENTIVE_PARAM_MONTH = $bulan;
                $boom->INCENTIVE_PARAM_YEAR = $tahun;
                $boom->INCENTIVE_PARAM_INCOME = $penghasilanfix;
                // $boom->INCENTIVE_PARAM_VALUTA = $valutafix;
                $boom->INCENTIVE_PARAM_DIST = $param;
                // $boom->INCENTIVE_PARAM_VALUE = $incentive_value;
                $boom->INCENTIVE_PARAM_VALUE = $insentifbudget;
                // $boom->INCENTIVE_PARAM_MIN_BOX_QCC = $qccfix;
                // $boom->INCENTIVE_PARAM_MIN_BOX_RTG = $rtgfix;

                $boom->save();

                $incentiveparamidmen = $boom->INCENTIVE_PARAM_ID;
                
            }


        } else {

            $cek = IncentiveParamModel::select('*')->where('INCENTIVE_PARAM_ID',$request->get('incentiveparam'))->first();
            if ($cek->INCENTIVE_PARAM_MONTH != $bulan OR $cek->INCENTIVE_PARAM_YEAR != $tahun OR $cek->INCENTIVE_PARAM_INCOME != $penghasilanfix OR ($cek->INCENTIVE_PARAM_CALCULATE_AGAIN == 1 AND $cek->INCENTIVE_PARAM_STATUS == 1)) {

                $deletepos = DB::table('tx_portion_of_slice')->where('INCENTIVE_PARAM_ID', '=', $request->get('incentiveparam'))->delete();
                $deletepob = DB::table('tx_portion_of_block')->where('INCENTIVE_PARAM_ID', '=', $request->get('incentiveparam'))->delete();
                // $deleteincentive = DB::table('tx_incentive_param')->where('INCENTIVE_PARAM_ID', '=', $request->get('incentiveparam'))->delete(); 

                $update = IncentiveParamModel::where('INCENTIVE_PARAM_ID', $request->get('incentiveparam'))->first();
                $update->INCENTIVE_PARAM_ID = $request->get('incentiveparam');
                $update->INCENTIVE_PARAM_MONTH = $bulan;
                $update->INCENTIVE_PARAM_YEAR = $tahun;
                $update->INCENTIVE_PARAM_INCOME = $penghasilanfix;
                // $update->INCENTIVE_PARAM_VALUTA = $valutafix;
                $update->INCENTIVE_PARAM_DIST = $param;
                $update->INCENTIVE_PARAM_VALUE = $insentifbudget;
                // $update->INCENTIVE_PARAM_MIN_BOX_QCC = $qccfix;
                // $update->INCENTIVE_PARAM_MIN_BOX_RTG = $rtgfix;

                $update->update();

                $incentiveparamidmen = $request->get('incentiveparam');
                
            }else {

                $updateParam = DB::table('tx_incentive_param')
                        ->where('INCENTIVE_PARAM_ID', $request->get('incentiveparam'))
                        ->update(['INCENTIVE_PARAM_STATUS' => 1]);

                $lastId = $request->get('incentiveparam');
                return Response::json(array('success'=>true,'lastid'=>$lastId));

            }


        }

        $portion = new PortionOfBlockModel();
        $portion->INCENTIVE_PARAM_ID = $incentiveparamidmen;
        $portion->BLOCK_ID = 1;
        $portion->POB_DIFF_PER_BLOCK = $diffBlock1;
        $portion->HEADACOUNT_PER_BLOCK = $countBlock1;
        $portion->POB_COEFFICIENT_UPPER_VALUE = $upperBlock1;
        $portion->POB_BOBOT = $bobot1;
        $portion->POB_PERSENTASE_BLOCK = $persenBlock1;
        $portion->POB_PERSENTASE_BLOCK_PER_EMP = $persenBlockKar1;
        $portion->POB_KUE_PER_BLOCK = $kueBlock1;
        $portion->POB_AVG_PER_EMP = $avgBlock1;
        $portion->POB_DIFF_NOMINAL = $diffNomBlock1;
        $portion->POB_PERSENTASE_DIFF_NOMINAL = $diffPersenBlock1;
        $portion->POB_BLOCK_COMPOSITION = $BlockPerCom1;

        $portion2 = new PortionOfBlockModel();
        $portion2->INCENTIVE_PARAM_ID = $incentiveparamidmen;
        $portion2->BLOCK_ID = 2;
        $portion2->POB_DIFF_PER_BLOCK = $diffBlock2;
        $portion2->HEADACOUNT_PER_BLOCK = $countBlock2;
        $portion2->POB_COEFFICIENT_UPPER_VALUE = $upperBlock2;
        $portion2->POB_BOBOT = $bobot2;
        $portion2->POB_PERSENTASE_BLOCK = $persenBlock2;
        $portion2->POB_PERSENTASE_BLOCK_PER_EMP = $persenBlockKar2;
        $portion2->POB_KUE_PER_BLOCK = $kueBlock2;
        $portion2->POB_AVG_PER_EMP = $avgBlock2;
        $portion2->POB_DIFF_NOMINAL = $diffNomBlock2;
        $portion2->POB_PERSENTASE_DIFF_NOMINAL = $diffPersenBlock2;
        $portion2->POB_BLOCK_COMPOSITION = $BlockPerCom2;

        $portion3 = new PortionOfBlockModel();
        $portion3->INCENTIVE_PARAM_ID = $incentiveparamidmen;
        $portion3->BLOCK_ID = 3;
        $portion3->POB_DIFF_PER_BLOCK = $diffBlock3;
        $portion3->HEADACOUNT_PER_BLOCK = $countBlock3;
        $portion3->POB_COEFFICIENT_UPPER_VALUE = $upperBlock3;
        $portion3->POB_BOBOT = $bobot3;
        $portion3->POB_PERSENTASE_BLOCK = $persenBlock3;
        $portion3->POB_PERSENTASE_BLOCK_PER_EMP = $persenBlockKar3;
        $portion3->POB_KUE_PER_BLOCK = $kueBlock3;
        $portion3->POB_AVG_PER_EMP = $avgBlock3;
        $portion3->POB_DIFF_NOMINAL = $diffNomBlock3;
        $portion3->POB_PERSENTASE_DIFF_NOMINAL = $diffPersenBlock3;
        $portion3->POB_BLOCK_COMPOSITION = $BlockPerCom3;


        $portion->save();
        $portion2->save();
        $portion3->save();

        ###### DISTRIBUTION ENGINE #########

        ### BLOCK 1 ###                
        $masterslice = DB::table('tm_employee')
                            ->select('EMPLOYEE_BLOCK','EMPLOYEE_SLICE',DB::raw('count(*) as count_slice'))
                            ->groupby('EMPLOYEE_SLICE','EMPLOYEE_BLOCK')
                            ->where('EMPLOYEE_BLOCK',1)
                            ->where('EMPLOYEE_STATUS',1)                            
                            ->get();        

        $AP_POS_CUV_BLOCK_I = $settingmaster->AP_POS_CUV_BLOCK_I; // 0.4536
        $AP_POS_DIFF_SLICE_BLOCK_I = $settingmaster->AP_POS_DIFF_SLICE_BLOCK_I; // 26.3000

        $arrayBlock1 = array();
        $i = 0;
        foreach($masterslice as $masterslices){

            $count = $masterslices->count_slice;
            $bobot = $AP_POS_CUV_BLOCK_I*$count;

            $arrayBlock1[$i] = array("cuv" => $AP_POS_CUV_BLOCK_I,
                                     "bobot" => $bobot,
                                     "jumlah_kar" => $count);

            $AP_POS_CUV_BLOCK_I = $AP_POS_CUV_BLOCK_I*(1-($AP_POS_DIFF_SLICE_BLOCK_I/100));

            $i++;
        }

        $sumBlock1 = array();

        array_walk_recursive($arrayBlock1, function($item, $key) use (&$sumBlock1){
            $sumBlock1[$key] = isset($sumBlock1[$key]) ?  $item + $sumBlock1[$key] : $item;
        });

        $sumBlock1fix = $sumBlock1['bobot'];

        $AP_POS_CUV_BLOCK_I2 = $settingmaster->AP_POS_CUV_BLOCK_I; // 0.4536
        $AP_POS_DIFF_SLICE_BLOCK_I2 = $settingmaster->AP_POS_DIFF_SLICE_BLOCK_I; // 26.3000
        $AP_POS_DIST_FLAT_BLOCK_I2 = $settingmaster->AP_POS_DIST_FLAT_BLOCK_I; // 25.0000

        foreach ($masterslice as $masterSliceBlock1) {

            $count2 = $masterSliceBlock1->count_slice;
            $bobot2 = $AP_POS_CUV_BLOCK_I2*$count2;
            $persenslice2 = $bobot2 / $sumBlock1fix * $BlockPerCom1; 
            $insentifpersenslice2 = $persenslice2/$count2; 
            $simKueSlice = $persenslice2 * $insentifbudget / 100;
            $incentiveFlatBlok1 = $AP_POS_DIST_FLAT_BLOCK_I2 / 100 * $simKueSlice; 

            $insertSlice = new PortionOfSliceModel();
            $insertSlice->INCENTIVE_PARAM_ID = $incentiveparamidmen;
            $insertSlice->BLOCK_ID = $masterSliceBlock1->EMPLOYEE_BLOCK;
            $insertSlice->SLICE_ID = $masterSliceBlock1->EMPLOYEE_SLICE;
            if ($masterSliceBlock1->EMPLOYEE_BLOCK == 1 and $masterSliceBlock1->EMPLOYEE_SLICE == 1) {
                $insertSlice->POS_DIFF_PER_SLICE = 2.00;
            } else {
                $insertSlice->POS_DIFF_PER_SLICE = $AP_POS_DIFF_SLICE_BLOCK_I2;
            }
            $insertSlice->POS_HEADACOUNT_PER_SLICE = $count2;
            $insertSlice->POS_COEFFICIENT_UPPER_VALUE = $AP_POS_CUV_BLOCK_I2;
            $insertSlice->POS_BOBOT = $bobot2;
            $insertSlice->POS_PERSENTASE_SLICE = $persenslice2;
            $insertSlice->POS_PERSENTASE_SLICE_PER_EMP = $insentifpersenslice2;
            $insertSlice->POS_KUE_PER_SLICE = $simKueSlice;
            $insertSlice->POS_NOM_FLAT_DIST_BLOCK_I = $incentiveFlatBlok1;

            $insertSlice->save();


            $AP_POS_CUV_BLOCK_I2 = $AP_POS_CUV_BLOCK_I2*(1-($AP_POS_DIFF_SLICE_BLOCK_I2/100));

        }

        ### END BLOCK 1 ###

        ### BLOCK 2 SHIFT ###

        $masterslice2 = DB::table('tr_slice')
                            ->select('SLICE_ID as EMPLOYEE_SLICE','SLICE_BLOCK as EMPLOYEE_BLOCK',
                                DB::raw('(SELECT COUNT(EMPLOYEE_NIK) FROM tm_employee WHERE EMPLOYEE_SLICE = tr_slice.SLICE_ID AND EMPLOYEE_BLOCK = 2 AND EMPLOYEE_STATUS = 1) as count_slice')
                                )
                            ->where('tr_slice.SLICE_BLOCK',2)
                            ->where('tr_slice.SLICE_NAME','LIKE','%Slice Shift%')
                            ->get();

        $AP_POS_CUV_BLOCK_II = $settingmaster->AP_POS_CUV_BLOCK_II; // 0.4536
        $AP_POS_DIFF_SLICE_BLOCK_IIA = $settingmaster->AP_POS_DIFF_SLICE_BLOCK_IIA; // 6.9000

        $arrayBlock2 = array();
        $j = 0;
        foreach($masterslice2 as $masterslice2s){

            $countII = $masterslice2s->count_slice;
            $bobotII = $AP_POS_CUV_BLOCK_II*$countII;

            $arrayBlock2[$j] = array("cuv" => $AP_POS_CUV_BLOCK_II,
                                     "bobot" => $bobotII,
                                     "jumlah_kar" => $countII);

            $AP_POS_CUV_BLOCK_II = $AP_POS_CUV_BLOCK_II*(1-($AP_POS_DIFF_SLICE_BLOCK_IIA/100));

            $j++;
        }

        $sumBlock2 = array();

        array_walk_recursive($arrayBlock2, function($item, $key) use (&$sumBlock2){
            $sumBlock2[$key] = isset($sumBlock2[$key]) ?  $item + $sumBlock2[$key] : $item;
        });

        $sumBlock2fix = $sumBlock2['bobot'];

        // SUM BOBOT NYOBA
        $masterslice2_non_sum = DB::table('tr_slice')
                            ->select('SLICE_ID as EMPLOYEE_SLICE','SLICE_BLOCK as EMPLOYEE_BLOCK',
                                DB::raw('(SELECT COUNT(EMPLOYEE_NIK) FROM tm_employee WHERE EMPLOYEE_SLICE = tr_slice.SLICE_ID AND EMPLOYEE_BLOCK = 2 AND EMPLOYEE_STATUS = 1) as count_slice')
                                )
                            ->where('tr_slice.SLICE_BLOCK',2)
                            ->where('tr_slice.SLICE_NAME','LIKE','%Slice Non-Shift%')
                            ->get();

        $AP_POS_CUV_BLOCK_II_non_sum = $settingmaster->AP_POS_CUV_BLOCK_II; // 0.4536
        $AP_POS_DIFF_SLICE_BLOCK_IIA_non_sum = $settingmaster->AP_POS_DIFF_SLICE_BLOCK_IIA; // 6.9000
        $AP_POS_DIFF_SLICE_BLOCK_IIB_non_sum = $settingmaster->AP_POS_DIFF_SLICE_BLOCK_IIB; // 15.0000

        $arrayBlock2_non_sum = array();
        $k_sum = 0;
        foreach($masterslice2_non_sum as $masterslice2s_non_sum){

            $countII_non_sum = $masterslice2s_non_sum->count_slice;

            $AP_POS_CUV_BLOCK_II_non2_sum = $AP_POS_CUV_BLOCK_II_non_sum*(1-($AP_POS_DIFF_SLICE_BLOCK_IIB_non_sum/100));
            $AP_POS_CUV_BLOCK_II_non_sum = $AP_POS_CUV_BLOCK_II_non_sum*(1-($AP_POS_DIFF_SLICE_BLOCK_IIA_non_sum/100));

            $bobotII_non_sum = $AP_POS_CUV_BLOCK_II_non2_sum*$countII_non_sum;

            // echo $bobotII_non_sum.'<br>';
            
            $arrayBlock2_non_sum[$k_sum] = array("cuv_sum" => $AP_POS_CUV_BLOCK_II_non2_sum,
                                     "bobot_sum" => $bobotII_non_sum,
                                     "jumlah_kar_sum" => $countII_non_sum);


            $k_sum++;
        }

        $sumBlock2_non_sum = array();

        array_walk_recursive($arrayBlock2_non_sum, function($item, $key) use (&$sumBlock2_non_sum){
            $sumBlock2_non_sum[$key] = isset($sumBlock2_non_sum[$key]) ?  $item + $sumBlock2_non_sum[$key] : $item;
        });

        $sumBlock2fix_non_sum = $sumBlock2_non_sum['bobot_sum'];
        
        $sumBlock2fix_men = $sumBlock2fix + $sumBlock2fix_non_sum;

        // END SUM BOBOT NYOBA

        $AP_POS_CUV_BLOCK_II2 = $settingmaster->AP_POS_CUV_BLOCK_II;
        $AP_POS_DIFF_SLICE_BLOCK_II2 = $settingmaster->AP_POS_DIFF_SLICE_BLOCK_IIA;
        $AP_POS_DIST_FLAT_BLOCK_II2 = $settingmaster->AP_POS_DIST_FLAT_BLOCK_I;

        foreach ($masterslice2 as $masterSliceBlock2) {
            $countII2 = $masterSliceBlock2->count_slice;
            if ($countII2 == 0) {
                $bobotII2 = 0;
                $persensliceII2 = 0; 
                $insentifpersensliceII2 = 0; 

                $simKueSliceII = 0;
                $incentiveFlatBlok2 = 0; 
            } else {   
                $bobotII2 = $AP_POS_CUV_BLOCK_II2*$countII2;
                $persensliceII2 = $bobotII2 / $sumBlock2fix_men * $BlockPerCom2; 
                $insentifpersensliceII2 = $persensliceII2/$countII2; 

                $simKueSliceII = $persensliceII2 * $insentifbudget / 100;
                $incentiveFlatBlok2 = $simKueSliceII; 
            }

            $insertSlice2Shift = new PortionOfSliceModel();
            $insertSlice2Shift->INCENTIVE_PARAM_ID = $incentiveparamidmen;
            $insertSlice2Shift->BLOCK_ID = $masterSliceBlock2->EMPLOYEE_BLOCK;
            $insertSlice2Shift->SLICE_ID = $masterSliceBlock2->EMPLOYEE_SLICE;
            $insertSlice2Shift->POS_DIFF_PER_SLICE = $AP_POS_DIFF_SLICE_BLOCK_II2;
            $insertSlice2Shift->POS_HEADACOUNT_PER_SLICE = $countII2;
            $insertSlice2Shift->POS_COEFFICIENT_UPPER_VALUE = $AP_POS_CUV_BLOCK_II2;
            $insertSlice2Shift->POS_BOBOT = $bobotII2;
            $insertSlice2Shift->POS_PERSENTASE_SLICE = $persensliceII2;
            $insertSlice2Shift->POS_PERSENTASE_SLICE_PER_EMP = $insentifpersensliceII2;
            $insertSlice2Shift->POS_KUE_PER_SLICE = $simKueSliceII;
            $insertSlice2Shift->POS_NOM_FLAT_DIST_BLOCK_I = $incentiveFlatBlok2;

            $insertSlice2Shift->save();


            $AP_POS_CUV_BLOCK_II2 = $AP_POS_CUV_BLOCK_II2*(1-($AP_POS_DIFF_SLICE_BLOCK_II2/100));

        }                

        ### END BLOCK 2 SHIFT ###

        ### BLOCK 2 NON SHIFT ###

        $masterslice2_non = DB::table('tr_slice')
                            ->select('SLICE_ID as EMPLOYEE_SLICE','SLICE_BLOCK as EMPLOYEE_BLOCK',
                                DB::raw('(SELECT COUNT(EMPLOYEE_NIK) FROM tm_employee WHERE EMPLOYEE_SLICE = tr_slice.SLICE_ID AND EMPLOYEE_BLOCK = 2 AND EMPLOYEE_STATUS = 1) as count_slice')
                                )
                            ->where('tr_slice.SLICE_BLOCK',2)
                            ->where('tr_slice.SLICE_NAME','LIKE','%Slice Non-Shift%')
                            ->get();

        $AP_POS_CUV_BLOCK_II_non = $settingmaster->AP_POS_CUV_BLOCK_II;
        $AP_POS_DIFF_SLICE_BLOCK_IIA_non = $settingmaster->AP_POS_DIFF_SLICE_BLOCK_IIA;
        $AP_POS_DIFF_SLICE_BLOCK_IIB_non = $settingmaster->AP_POS_DIFF_SLICE_BLOCK_IIB;

        $arrayBlock2_non = array();
        $k = 0;
        foreach($masterslice2_non as $masterslice2s_non){

            $countII_non = $masterslice2s_non->count_slice;

            $AP_POS_CUV_BLOCK_II_non2 = $AP_POS_CUV_BLOCK_II_non*(1-($AP_POS_DIFF_SLICE_BLOCK_IIB_non/100));
            $AP_POS_CUV_BLOCK_II_non = $AP_POS_CUV_BLOCK_II_non*(1-($AP_POS_DIFF_SLICE_BLOCK_IIA_non/100));
            
            $bobotII_non = $AP_POS_CUV_BLOCK_II_non2*$countII_non;
            
            $arrayBlock2_non[$k] = array("cuv" => $AP_POS_CUV_BLOCK_II_non2,
                                     "bobot" => $bobotII_non,
                                     "jumlah_kar" => $countII_non);


            $k++;
        }

        $sumBlock2_non = array();

        array_walk_recursive($arrayBlock2_non, function($item, $key) use (&$sumBlock2_non){
            $sumBlock2_non[$key] = isset($sumBlock2_non[$key]) ?  $item + $sumBlock2_non[$key] : $item;
        });

        $sumBlock2fix_non = $sumBlock2_non['bobot'];

        // SUM BOBOT NYOBA
         $masterslice2_sum = DB::table('tr_slice')
                            ->select('SLICE_ID as EMPLOYEE_SLICE','SLICE_BLOCK as EMPLOYEE_BLOCK',
                                DB::raw('(SELECT COUNT(EMPLOYEE_NIK) FROM tm_employee WHERE EMPLOYEE_SLICE = tr_slice.SLICE_ID AND EMPLOYEE_BLOCK = 2 AND EMPLOYEE_STATUS = 1) as count_slice')
                                )
                            ->where('tr_slice.SLICE_BLOCK',2)
                            ->where('tr_slice.SLICE_NAME','LIKE','%Slice Shift%')
                            ->get();

        $AP_POS_CUV_BLOCK_II_sum = $settingmaster->AP_POS_CUV_BLOCK_II;
        $AP_POS_DIFF_SLICE_BLOCK_IIA_sum = $settingmaster->AP_POS_DIFF_SLICE_BLOCK_IIA;

        $arrayBlock2_sum = array();
        $j_sum = 0;
        foreach($masterslice2_sum as $masterslice2s_sum){

            $countII_sum = $masterslice2s_sum->count_slice;
            $bobotII_sum = $AP_POS_CUV_BLOCK_II_sum*$countII_sum;

            $arrayBlock2_sum[$j_sum] = array("cuv_sum" => $AP_POS_CUV_BLOCK_II_sum,
                                     "bobot_sum" => $bobotII_sum,
                                     "jumlah_kar_sum" => $countII_sum);

            $AP_POS_CUV_BLOCK_II_sum = $AP_POS_CUV_BLOCK_II_sum*(1-($AP_POS_DIFF_SLICE_BLOCK_IIA_sum/100));

            $j_sum++;
        }

        $sumBlock2_sum = array();

        array_walk_recursive($arrayBlock2_sum, function($item, $key) use (&$sumBlock2_sum){
            $sumBlock2_sum[$key] = isset($sumBlock2_sum[$key]) ?  $item + $sumBlock2_sum[$key] : $item;
        });

        $sumBlock2fix_sum = $sumBlock2_sum['bobot_sum'];
        
        $sumBlock2fix_non_men = $sumBlock2fix_non + $sumBlock2fix_sum;

        // END SUM BOBOT NYOBA

        $AP_POS_CUV_BLOCK_II2_non = $settingmaster->AP_POS_CUV_BLOCK_II;
        $AP_POS_DIFF_SLICE_BLOCK_IIA2_non = $settingmaster->AP_POS_DIFF_SLICE_BLOCK_IIA;
        $AP_POS_DIFF_SLICE_BLOCK_IIB2_non = $settingmaster->AP_POS_DIFF_SLICE_BLOCK_IIB;
        $AP_POS_DIST_FLAT_BLOCK_II2_non = $settingmaster->AP_POS_DIST_FLAT_BLOCK_I;
        
        foreach ($masterslice2_non as $masterSliceBlock2_non) {
            $countII2_non = $masterSliceBlock2_non->count_slice;

            $AP_POS_CUV_BLOCK_II2_non2 = $AP_POS_CUV_BLOCK_II2_non*(1-($AP_POS_DIFF_SLICE_BLOCK_IIB2_non/100));
            $AP_POS_CUV_BLOCK_II2_non = $AP_POS_CUV_BLOCK_II2_non*(1-($AP_POS_DIFF_SLICE_BLOCK_IIA2_non/100));
            if ($countII2_non == 0) {
                $bobotII2_non = 0;
                $persensliceII2_non = 0; 
                $insentifpersensliceII2_non = 0; 

                $simKueSliceII_non = 0;
                $incentiveFlatBlok2_non = 0; 
            } else {   
                $bobotII2_non = $AP_POS_CUV_BLOCK_II2_non2*$countII2_non;
                $persensliceII2_non = $bobotII2_non / $sumBlock2fix_non_men * $BlockPerCom2; 
                $insentifpersensliceII2_non = $persensliceII2_non/$countII2_non; 

                $simKueSliceII_non = $persensliceII2_non * $insentifbudget / 100;
                $incentiveFlatBlok2_non = $simKueSliceII_non; 
            }

            $insertSlice2Shift_non = new PortionOfSliceModel();
            $insertSlice2Shift_non->INCENTIVE_PARAM_ID = $incentiveparamidmen;
            $insertSlice2Shift_non->BLOCK_ID = $masterSliceBlock2_non->EMPLOYEE_BLOCK;
            $insertSlice2Shift_non->SLICE_ID = $masterSliceBlock2_non->EMPLOYEE_SLICE;
            $insertSlice2Shift_non->POS_DIFF_PER_SLICE = $AP_POS_DIFF_SLICE_BLOCK_IIB2_non;
            $insertSlice2Shift_non->POS_HEADACOUNT_PER_SLICE = $countII2_non;
            $insertSlice2Shift_non->POS_COEFFICIENT_UPPER_VALUE = $AP_POS_CUV_BLOCK_II2_non2;
            $insertSlice2Shift_non->POS_BOBOT = $bobotII2_non;
            $insertSlice2Shift_non->POS_PERSENTASE_SLICE = $persensliceII2_non;
            $insertSlice2Shift_non->POS_PERSENTASE_SLICE_PER_EMP = $insentifpersensliceII2_non;
            $insertSlice2Shift_non->POS_KUE_PER_SLICE = $simKueSliceII_non;
            $insertSlice2Shift_non->POS_NOM_FLAT_DIST_BLOCK_I = $incentiveFlatBlok2_non;

            $insertSlice2Shift_non->save();


        }               

        ### END BLOCK 2 NON SHIFT ###

        ### BLOCK 3 NON SHIFT ###

        $masterslice3 = DB::table('tr_slice')
                            ->select('SLICE_ID as EMPLOYEE_SLICE','SLICE_BLOCK as EMPLOYEE_BLOCK',
                                DB::raw('(SELECT COUNT(EMPLOYEE_NIK) FROM tm_employee WHERE EMPLOYEE_SLICE = tr_slice.SLICE_ID AND EMPLOYEE_BLOCK = 3 AND EMPLOYEE_STATUS = 1) as count_slice')
                                )
                            ->where('tr_slice.SLICE_BLOCK',3)
                            ->get();

        $AP_POS_CUV_BLOCK_III = $settingmaster->AP_POS_CUV_BLOCK_III;
        $AP_POS_DIFF_SLICE_BLOCK_III = $settingmaster->AP_POS_DIFF_SLICE_BLOCK_III;

        $arrayBlock3 = array();
        $l = 0;
        foreach($masterslice3 as $masterslice3s){

            $countIII = $masterslice3s->count_slice;
            $bobotIII = $AP_POS_CUV_BLOCK_III*$countIII;

            $arrayBlock3[$l] = array("cuv" => $AP_POS_CUV_BLOCK_III,
                                     "bobot" => $bobotIII,
                                     "jumlah_kar" => $countIII);

            $AP_POS_CUV_BLOCK_III = $AP_POS_CUV_BLOCK_III*(1-($AP_POS_DIFF_SLICE_BLOCK_III/100));

            $l++;
        }

        $sumBlock2 = array();

        array_walk_recursive($arrayBlock3, function($item, $key) use (&$sumBlock3){
            $sumBlock3[$key] = isset($sumBlock3[$key]) ?  $item + $sumBlock3[$key] : $item;
        });

        $sumBlock3fix = $sumBlock3['bobot'];

        $AP_POS_CUV_BLOCK_III2 = $settingmaster->AP_POS_CUV_BLOCK_III;
        $AP_POS_DIFF_SLICE_BLOCK_III2 = $settingmaster->AP_POS_DIFF_SLICE_BLOCK_III;
        $AP_POS_DIST_FLAT_BLOCK_III2 = $settingmaster->AP_POS_DIST_FLAT_BLOCK_I;

        foreach ($masterslice3 as $masterSliceBlock3) {
            $countIII2 = $masterSliceBlock3->count_slice;
            if ($countIII2 == 0) {
                $bobotIII2 = 0;
                $persensliceIII2 = 0; 
                $insentifpersensliceIII2 = 0; 

                $simKueSliceIII = 0;
                $incentiveFlatBlok3 = 0; 
            } else {   
                $bobotIII2 = $AP_POS_CUV_BLOCK_III2*$countIII2;
                $persensliceIII2 = $bobotIII2 / $sumBlock3fix * $BlockPerCom3; 
                $insentifpersensliceIII2 = $persensliceIII2/$countIII2; 

                $simKueSliceIII = $persensliceIII2 * $insentifbudget / 100;
                $incentiveFlatBlok3 = $simKueSliceIII; 
            }

            $insertSlice3NonShift = new PortionOfSliceModel();
            $insertSlice3NonShift->INCENTIVE_PARAM_ID = $incentiveparamidmen;
            $insertSlice3NonShift->BLOCK_ID = $masterSliceBlock3->EMPLOYEE_BLOCK;
            $insertSlice3NonShift->SLICE_ID = $masterSliceBlock3->EMPLOYEE_SLICE;
            $insertSlice3NonShift->POS_DIFF_PER_SLICE = $AP_POS_DIFF_SLICE_BLOCK_III2;
            $insertSlice3NonShift->POS_HEADACOUNT_PER_SLICE = $countIII2;
            $insertSlice3NonShift->POS_COEFFICIENT_UPPER_VALUE = $AP_POS_CUV_BLOCK_III2;
            $insertSlice3NonShift->POS_BOBOT = $bobotIII2;
            $insertSlice3NonShift->POS_PERSENTASE_SLICE = $persensliceIII2;
            $insertSlice3NonShift->POS_PERSENTASE_SLICE_PER_EMP = $insentifpersensliceIII2;
            $insertSlice3NonShift->POS_KUE_PER_SLICE = $simKueSliceIII;
            $insertSlice3NonShift->POS_NOM_FLAT_DIST_BLOCK_I = $incentiveFlatBlok3;

            $insertSlice3NonShift->save();

            $AP_POS_CUV_BLOCK_III2 = $AP_POS_CUV_BLOCK_III2*(1-($AP_POS_DIFF_SLICE_BLOCK_III2/100));

        }            

        ### END BLOCK 3 NON SHIFT ###

        //update calculate again become 0

        $updateParam = DB::table('tx_incentive_param')
                        ->where('INCENTIVE_PARAM_ID', $incentiveparamidmen)
                        ->update(['INCENTIVE_PARAM_STATUS' => 1, 'INCENTIVE_PARAM_CALCULATE_AGAIN' => 0]);

        $lastId = $incentiveparamidmen;
        return Response::json(array('success'=>true,'lastid'=>$lastId));

   
    }

    public function saveStep2(Request $request){
        
        $param = $request->get('incentiveparam');
        // echo $param;
        // $minmaster = DB::table('tx_incentive_param')
        //                 ->select('INCENTIVE_PARAM_MIN_BOX_RTG','INCENTIVE_PARAM_MIN_BOX_QCC')
        //                 ->where('INCENTIVE_PARAM_ID',$param)
        //                 ->first();

        $minmaster = AdjustParamModel::select('AP_BA_MIN_BOX_QCC','AP_BA_MIN_BOX_RTG')->where('ADJUST_PARAM_ID',1)->first();

        $minboxrtg = $minmaster->AP_BA_MIN_BOX_RTG;
        $minboxqcc = $minmaster->AP_BA_MIN_BOX_QCC;

        $incentivesliceqcc = DB::table('tx_portion_of_slice')
                                ->select('POS_KUE_PER_SLICE','POS_NOM_FLAT_DIST_BLOCK_I')
                                ->where('BLOCK_ID',1)->where('SLICE_ID',1)
                                ->where('INCENTIVE_PARAM_ID',$param)
                                ->first();
                                
        $incentivesliceqccs = $incentivesliceqcc->POS_KUE_PER_SLICE - $incentivesliceqcc->POS_NOM_FLAT_DIST_BLOCK_I; // 25% dari kue per slice 


        $incentiveslicertg = DB::table('tx_portion_of_slice')
                                ->select('POS_KUE_PER_SLICE','POS_NOM_FLAT_DIST_BLOCK_I')
                                ->where('BLOCK_ID',1)
                                ->where('SLICE_ID',2)
                                ->where('INCENTIVE_PARAM_ID',$param)
                                ->first();                                
        $incentiveslicertgs = $incentiveslicertg->POS_KUE_PER_SLICE- $incentiveslicertg->POS_NOM_FLAT_DIST_BLOCK_I;    

        $sumboxqcc = $request->get('QccboxpergroupA') + $request->get('QccboxpergroupB') + $request->get('QccboxpergroupC') + $request->get('QccboxpergroupD');
        $sumboxrtg = $request->get('RtgboxpergroupA') + $request->get('RtgboxpergroupB') + $request->get('RtgboxpergroupC') + $request->get('RtgboxpergroupD');

        $GroupInQCCA = $request->get('QccboxpergroupA') / $sumboxqcc * $incentivesliceqccs;
        $GroupInQCCB = $request->get('QccboxpergroupB') / $sumboxqcc * $incentivesliceqccs;
        $GroupInQCCC = $request->get('QccboxpergroupC') / $sumboxqcc * $incentivesliceqccs;
        $GroupInQCCD = $request->get('QccboxpergroupD') / $sumboxqcc * $incentivesliceqccs;

        $GroupInRTGA = $request->get('RtgboxpergroupA') / $sumboxrtg * $incentiveslicertgs;
        $GroupInRTGB = $request->get('RtgboxpergroupB') / $sumboxrtg * $incentiveslicertgs;
        $GroupInRTGC = $request->get('RtgboxpergroupC') / $sumboxrtg * $incentiveslicertgs;
        $GroupInRTGD = $request->get('RtgboxpergroupD') / $sumboxrtg * $incentiveslicertgs;


        $HrgInQCCA = $GroupInQCCA / $request->get('QccboxpergroupA');
        $HrgInQCCB = $GroupInQCCB / $request->get('QccboxpergroupB');
        $HrgInQCCC = $GroupInQCCC / $request->get('QccboxpergroupC');
        $HrgInQCCD = $GroupInQCCD / $request->get('QccboxpergroupD');

        $HrgInRTGA = $GroupInRTGA / $request->get('RtgboxpergroupA');
        $HrgInRTGB = $GroupInRTGB / $request->get('RtgboxpergroupB');
        $HrgInRTGC = $GroupInRTGC / $request->get('RtgboxpergroupC');
        $HrgInRTGD = $GroupInRTGD / $request->get('RtgboxpergroupD');

        // return $GroupInQCCA;

        if ($request->get('BOX_ADJUSMENT_GROUP_ID_QCCA') == "" OR $request->get('BOX_ADJUSMENT_GROUP_ID_QCCA') == null) {

            $insertGroupQCCA = new BoxAdjusmentGroupModel();        
            $insertGroupQCCA->INCENTIVE_PARAM_ID = $param;
            $insertGroupQCCA->GROUP_ID = 8;
            $insertGroupQCCA->POSITION_ID = 66;
            $insertGroupQCCA->BOX_ADJUSMENT_GROUP_VALUE = $request->get('QccboxpergroupA');
            $insertGroupQCCA->BOX_ADJUSMENT_GROUP_INCENTIVE = $GroupInQCCA;
            $insertGroupQCCA->BOX_ADJUSMENT_GROUP_PRICE = $HrgInQCCA;

            $insertGroupQCCB = new BoxAdjusmentGroupModel();
            $insertGroupQCCB->INCENTIVE_PARAM_ID = $param;
            $insertGroupQCCB->GROUP_ID = 9;
            $insertGroupQCCB->POSITION_ID = 66;
            $insertGroupQCCB->BOX_ADJUSMENT_GROUP_VALUE = $request->get('QccboxpergroupB');
            $insertGroupQCCB->BOX_ADJUSMENT_GROUP_INCENTIVE = $GroupInQCCB;
            $insertGroupQCCB->BOX_ADJUSMENT_GROUP_PRICE = $HrgInQCCB;

            $insertGroupQCCC = new BoxAdjusmentGroupModel();
            $insertGroupQCCC->INCENTIVE_PARAM_ID = $param;
            $insertGroupQCCC->GROUP_ID = 10;
            $insertGroupQCCC->POSITION_ID = 66;
            $insertGroupQCCC->BOX_ADJUSMENT_GROUP_VALUE = $request->get('QccboxpergroupC');
            $insertGroupQCCC->BOX_ADJUSMENT_GROUP_INCENTIVE = $GroupInQCCC;
            $insertGroupQCCC->BOX_ADJUSMENT_GROUP_PRICE = $HrgInQCCC;

            $insertGroupQCCD = new BoxAdjusmentGroupModel();
            $insertGroupQCCD->INCENTIVE_PARAM_ID = $param;
            $insertGroupQCCD->GROUP_ID = 11;
            $insertGroupQCCD->POSITION_ID = 66;
            $insertGroupQCCD->BOX_ADJUSMENT_GROUP_VALUE = $request->get('QccboxpergroupD');
            $insertGroupQCCD->BOX_ADJUSMENT_GROUP_INCENTIVE = $GroupInQCCD;
            $insertGroupQCCD->BOX_ADJUSMENT_GROUP_PRICE = $HrgInQCCD;

            $insertGroupRTGA = new BoxAdjusmentGroupModel();        
            $insertGroupRTGA->INCENTIVE_PARAM_ID = $param;
            $insertGroupRTGA->GROUP_ID = 8;
            $insertGroupRTGA->POSITION_ID = 67;
            $insertGroupRTGA->BOX_ADJUSMENT_GROUP_VALUE = $request->get('RtgboxpergroupA');
            $insertGroupRTGA->BOX_ADJUSMENT_GROUP_INCENTIVE = $GroupInRTGA;
            $insertGroupRTGA->BOX_ADJUSMENT_GROUP_PRICE = $HrgInRTGA;

            $insertGroupRTGB = new BoxAdjusmentGroupModel();
            $insertGroupRTGB->INCENTIVE_PARAM_ID = $param;
            $insertGroupRTGB->GROUP_ID = 9;
            $insertGroupRTGB->POSITION_ID = 67;
            $insertGroupRTGB->BOX_ADJUSMENT_GROUP_VALUE = $request->get('RtgboxpergroupB');
            $insertGroupRTGB->BOX_ADJUSMENT_GROUP_INCENTIVE = $GroupInRTGB;
            $insertGroupRTGB->BOX_ADJUSMENT_GROUP_PRICE = $HrgInRTGB;

            $insertGroupRTGC = new BoxAdjusmentGroupModel();
            $insertGroupRTGC->INCENTIVE_PARAM_ID = $param;
            $insertGroupRTGC->GROUP_ID = 10;
            $insertGroupRTGC->POSITION_ID = 67;
            $insertGroupRTGC->BOX_ADJUSMENT_GROUP_VALUE = $request->get('RtgboxpergroupC');
            $insertGroupRTGC->BOX_ADJUSMENT_GROUP_INCENTIVE = $GroupInRTGC;
            $insertGroupRTGC->BOX_ADJUSMENT_GROUP_PRICE = $HrgInRTGC;

            $insertGroupRTGD = new BoxAdjusmentGroupModel();
            $insertGroupRTGD->INCENTIVE_PARAM_ID = $param;
            $insertGroupRTGD->GROUP_ID = 11;
            $insertGroupRTGD->POSITION_ID = 67;
            $insertGroupRTGD->BOX_ADJUSMENT_GROUP_VALUE = $request->get('RtgboxpergroupD');
            $insertGroupRTGD->BOX_ADJUSMENT_GROUP_INCENTIVE = $GroupInRTGD;
            $insertGroupRTGD->BOX_ADJUSMENT_GROUP_PRICE = $HrgInRTGD;

            $insertGroupQCCA->save();
            $insertGroupQCCB->save();
            $insertGroupQCCC->save();
            $insertGroupQCCD->save();

            $insertGroupRTGA->save();
            $insertGroupRTGB->save();
            $insertGroupRTGC->save();
            $insertGroupRTGD->save();

            $idqcca = $insertGroupQCCA->BOX_ADJUSMENT_GROUP_ID;
            $idqccb = $insertGroupQCCB->BOX_ADJUSMENT_GROUP_ID;
            $idqccc = $insertGroupQCCC->BOX_ADJUSMENT_GROUP_ID;
            $idqccd = $insertGroupQCCD->BOX_ADJUSMENT_GROUP_ID;

            $idrtga = $insertGroupRTGA->BOX_ADJUSMENT_GROUP_ID;
            $idrtgb = $insertGroupRTGB->BOX_ADJUSMENT_GROUP_ID;
            $idrtgc = $insertGroupRTGC->BOX_ADJUSMENT_GROUP_ID;
            $idrtgd = $insertGroupRTGD->BOX_ADJUSMENT_GROUP_ID;

            
        } else {

            $idqcca = $request->get('BOX_ADJUSMENT_GROUP_ID_QCCA');
            $idqccb = $request->get('BOX_ADJUSMENT_GROUP_ID_QCCB');
            $idqccc = $request->get('BOX_ADJUSMENT_GROUP_ID_QCCC');
            $idqccd = $request->get('BOX_ADJUSMENT_GROUP_ID_QCCD');

            $idrtga = $request->get('BOX_ADJUSMENT_GROUP_ID_RTGA');
            $idrtgb = $request->get('BOX_ADJUSMENT_GROUP_ID_RTGB');
            $idrtgc = $request->get('BOX_ADJUSMENT_GROUP_ID_RTGC');
            $idrtgd = $request->get('BOX_ADJUSMENT_GROUP_ID_RTGD');

            // $delete = DB::table('tx_box_adjusment_group')->where('INCENTIVE_PARAM_ID', '=', $param)->delete();

            $updateGroupQCCA = BoxAdjusmentGroupModel::where('BOX_ADJUSMENT_GROUP_ID', $idqcca)->first();
            $updateGroupQCCA->INCENTIVE_PARAM_ID = $param;
            $updateGroupQCCA->GROUP_ID = 8;
            $updateGroupQCCA->POSITION_ID = 66;
            $updateGroupQCCA->BOX_ADJUSMENT_GROUP_VALUE = $request->get('QccboxpergroupA');
            $updateGroupQCCA->BOX_ADJUSMENT_GROUP_INCENTIVE = $GroupInQCCA;
            $updateGroupQCCA->BOX_ADJUSMENT_GROUP_PRICE = $HrgInQCCA;

            $updateGroupQCCB = BoxAdjusmentGroupModel::where('BOX_ADJUSMENT_GROUP_ID', $idqccb)->first();
            $updateGroupQCCB->INCENTIVE_PARAM_ID = $param;
            $updateGroupQCCB->GROUP_ID = 9;
            $updateGroupQCCB->POSITION_ID = 66;
            $updateGroupQCCB->BOX_ADJUSMENT_GROUP_VALUE = $request->get('QccboxpergroupB');
            $updateGroupQCCB->BOX_ADJUSMENT_GROUP_INCENTIVE = $GroupInQCCB;
            $updateGroupQCCB->BOX_ADJUSMENT_GROUP_PRICE = $HrgInQCCB;

            $updateGroupQCCC = BoxAdjusmentGroupModel::where('BOX_ADJUSMENT_GROUP_ID', $idqccc)->first();
            $updateGroupQCCC->INCENTIVE_PARAM_ID = $param;
            $updateGroupQCCC->GROUP_ID = 10;
            $updateGroupQCCC->POSITION_ID = 66;
            $updateGroupQCCC->BOX_ADJUSMENT_GROUP_VALUE = $request->get('QccboxpergroupC');
            $updateGroupQCCC->BOX_ADJUSMENT_GROUP_INCENTIVE = $GroupInQCCC;
            $updateGroupQCCC->BOX_ADJUSMENT_GROUP_PRICE = $HrgInQCCC;

            $updateGroupQCCD = BoxAdjusmentGroupModel::where('BOX_ADJUSMENT_GROUP_ID', $idqccd)->first();
            $updateGroupQCCD->INCENTIVE_PARAM_ID = $param;
            $updateGroupQCCD->GROUP_ID = 11;
            $updateGroupQCCD->POSITION_ID = 66;
            $updateGroupQCCD->BOX_ADJUSMENT_GROUP_VALUE = $request->get('QccboxpergroupD');
            $updateGroupQCCD->BOX_ADJUSMENT_GROUP_INCENTIVE = $GroupInQCCD;
            $updateGroupQCCD->BOX_ADJUSMENT_GROUP_PRICE = $HrgInQCCD;

            $updateGroupRTGA = BoxAdjusmentGroupModel::where('BOX_ADJUSMENT_GROUP_ID', $idrtga)->first();
            $updateGroupRTGA->INCENTIVE_PARAM_ID = $param;
            $updateGroupRTGA->GROUP_ID = 8;
            $updateGroupRTGA->POSITION_ID = 67;
            $updateGroupRTGA->BOX_ADJUSMENT_GROUP_VALUE = $request->get('RtgboxpergroupA');
            $updateGroupRTGA->BOX_ADJUSMENT_GROUP_INCENTIVE = $GroupInRTGA;
            $updateGroupRTGA->BOX_ADJUSMENT_GROUP_PRICE = $HrgInRTGA;

            $updateGroupRTGB = BoxAdjusmentGroupModel::where('BOX_ADJUSMENT_GROUP_ID', $idrtgb)->first();
            $updateGroupRTGB->INCENTIVE_PARAM_ID = $param;
            $updateGroupRTGB->GROUP_ID = 9;
            $updateGroupRTGB->POSITION_ID = 67;
            $updateGroupRTGB->BOX_ADJUSMENT_GROUP_VALUE = $request->get('RtgboxpergroupB');
            $updateGroupRTGB->BOX_ADJUSMENT_GROUP_INCENTIVE = $GroupInRTGB;
            $updateGroupRTGB->BOX_ADJUSMENT_GROUP_PRICE = $HrgInRTGB;

            $updateGroupRTGC = BoxAdjusmentGroupModel::where('BOX_ADJUSMENT_GROUP_ID', $idrtgc)->first();
            $updateGroupRTGC->INCENTIVE_PARAM_ID = $param;
            $updateGroupRTGC->GROUP_ID = 10;
            $updateGroupRTGC->POSITION_ID = 67;
            $updateGroupRTGC->BOX_ADJUSMENT_GROUP_VALUE = $request->get('RtgboxpergroupC');
            $updateGroupRTGC->BOX_ADJUSMENT_GROUP_INCENTIVE = $GroupInRTGC;
            $updateGroupRTGC->BOX_ADJUSMENT_GROUP_PRICE = $HrgInRTGC;

            $updateGroupRTGD = BoxAdjusmentGroupModel::where('BOX_ADJUSMENT_GROUP_ID', $idrtgd)->first();
            $updateGroupRTGD->INCENTIVE_PARAM_ID = $param;
            $updateGroupRTGD->GROUP_ID = 11;
            $updateGroupRTGD->POSITION_ID = 67;
            $updateGroupRTGD->BOX_ADJUSMENT_GROUP_VALUE = $request->get('RtgboxpergroupD');
            $updateGroupRTGD->BOX_ADJUSMENT_GROUP_INCENTIVE = $GroupInRTGD;
            $updateGroupRTGD->BOX_ADJUSMENT_GROUP_PRICE = $HrgInRTGD;

            $updateGroupQCCA->update();
            $updateGroupQCCB->update();
            $updateGroupQCCC->update();
            $updateGroupQCCD->update();

            $updateGroupRTGA->update();
            $updateGroupRTGB->update();
            $updateGroupRTGC->update();
            $updateGroupRTGD->update();

        }

        $updateParam = DB::table('tx_incentive_param')
                        ->where('INCENTIVE_PARAM_ID', $param)
                        ->update(['INCENTIVE_PARAM_STATUS' => 2]);

        $selectsumboxorang = BoxAdjusmentGroupModel::select('tx_box_adjusment_group.BOX_ADJUSMENT_GROUP_ID','tx_box_adjusment_group.BOX_ADJUSMENT_GROUP_VALUE',
                                                    DB::raw('CASE WHEN SUM(tx_box_adjusment_emp.BAE_BOX_HANDLE) IS NULL THEN 0 ELSE SUM(tx_box_adjusment_emp.BAE_BOX_HANDLE) END AS sum'))
                                                    ->leftjoin('tx_box_adjusment_emp','tx_box_adjusment_emp.BOX_ADJUSMENT_GROUP_ID','=','tx_box_adjusment_group.BOX_ADJUSMENT_GROUP_ID')
                                                    ->where('tx_box_adjusment_group.INCENTIVE_PARAM_ID',$param)
                                                    ->groupby('tx_box_adjusment_group.BOX_ADJUSMENT_GROUP_ID','tx_box_adjusment_group.BOX_ADJUSMENT_GROUP_VALUE')
                                                    ->get();

        // return $selectsumboxorang;
        $sumboxorangqcca = $selectsumboxorang[0]->sum;
        $sumboxorangqccb = $selectsumboxorang[1]->sum;
        $sumboxorangqccc = $selectsumboxorang[2]->sum;
        $sumboxorangqccd = $selectsumboxorang[3]->sum;

        $sumboxorangrtga = $selectsumboxorang[4]->sum;
        $sumboxorangrtgb = $selectsumboxorang[5]->sum;
        $sumboxorangrtgc = $selectsumboxorang[6]->sum;
        $sumboxorangrtgd = $selectsumboxorang[7]->sum;

        $overboxqcca = $selectsumboxorang[0]->BOX_ADJUSMENT_GROUP_VALUE - $selectsumboxorang[0]->sum;
        $overboxqccb = $selectsumboxorang[1]->BOX_ADJUSMENT_GROUP_VALUE - $selectsumboxorang[1]->sum;
        $overboxqccc = $selectsumboxorang[2]->BOX_ADJUSMENT_GROUP_VALUE - $selectsumboxorang[2]->sum;
        $overboxqccd = $selectsumboxorang[3]->BOX_ADJUSMENT_GROUP_VALUE - $selectsumboxorang[3]->sum;

        $overboxrtga = $selectsumboxorang[4]->BOX_ADJUSMENT_GROUP_VALUE - $selectsumboxorang[4]->sum;
        $overboxrtgb = $selectsumboxorang[5]->BOX_ADJUSMENT_GROUP_VALUE - $selectsumboxorang[5]->sum;
        $overboxrtgc = $selectsumboxorang[6]->BOX_ADJUSMENT_GROUP_VALUE - $selectsumboxorang[6]->sum;
        $overboxrtgd = $selectsumboxorang[7]->BOX_ADJUSMENT_GROUP_VALUE - $selectsumboxorang[7]->sum;

        return Response::json(array(
                                    'success'=>true, 
                                    'BoxPerGroupA'=>$request->get('QccboxpergroupA'),
                                    'BoxPerGroupB'=>$request->get('QccboxpergroupB'),
                                    'BoxPerGroupC'=>$request->get('QccboxpergroupC'),
                                    'BoxPerGroupD'=>$request->get('QccboxpergroupD'),
                                    'RtgBoxPerGroupA'=>$request->get('RtgboxpergroupA'),
                                    'RtgBoxPerGroupB'=>$request->get('RtgboxpergroupB'),
                                    'RtgBoxPerGroupC'=>$request->get('RtgboxpergroupC'),
                                    'RtgBoxPerGroupD'=>$request->get('RtgboxpergroupD'),

                                    'InsentifPerUnitA'=>$GroupInQCCA,
                                    'InsentifPerUnitB'=>$GroupInQCCB,
                                    'InsentifPerUnitC'=>$GroupInQCCC,
                                    'InsentifPerUnitD'=>$GroupInQCCD,
                                    'RtgInsentifPerUnitA'=>$GroupInRTGA,
                                    'RtgInsentifPerUnitB'=>$GroupInRTGB,
                                    'RtgInsentifPerUnitC'=>$GroupInRTGC,
                                    'RtgInsentifPerUnitD'=>$GroupInRTGD,

                                    'InsentifPerBoxA'=>$HrgInQCCA,
                                    'InsentifPerBoxB'=>$HrgInQCCB,
                                    'InsentifPerBoxC'=>$HrgInQCCC,
                                    'InsentifPerBoxD'=>$HrgInQCCD,
                                    'RtgInsentifPerBoxA'=>$HrgInRTGA,
                                    'RtgInsentifPerBoxB'=>$HrgInRTGB,
                                    'RtgInsentifPerBoxC'=>$HrgInRTGC,
                                    'RtgInsentifPerBoxD'=>$HrgInRTGD,

                                    'BOX_ADJUSMENT_GROUP_ID_QCCA'=>$idqcca,
                                    'BOX_ADJUSMENT_GROUP_ID_QCCB'=>$idqccb,
                                    'BOX_ADJUSMENT_GROUP_ID_QCCC'=>$idqccc,
                                    'BOX_ADJUSMENT_GROUP_ID_QCCD'=>$idqccd,
                                    'BOX_ADJUSMENT_GROUP_ID_RTGA'=>$idrtga,
                                    'BOX_ADJUSMENT_GROUP_ID_RTGB'=>$idrtgb,
                                    'BOX_ADJUSMENT_GROUP_ID_RTGC'=>$idrtgc,
                                    'BOX_ADJUSMENT_GROUP_ID_RTGD'=>$idrtgd,

                                    'minboxrtg'=>$minboxrtg,
                                    'minboxqcc'=>$minboxqcc,
                                    'kueinsqcc'=>$incentivesliceqccs,
                                    'kueinsrtg'=>$incentiveslicertgs,

                                    'BoxPerOrangA' => $sumboxorangqcca,
                                    'BoxPerOrangB' => $sumboxorangqccb,
                                    'BoxPerOrangC' => $sumboxorangqccc,
                                    'BoxPerOrangD' => $sumboxorangqccd,

                                    'RtgBoxPerOrangA' => $sumboxorangrtga,
                                    'RtgBoxPerOrangB' => $sumboxorangrtgb,
                                    'RtgBoxPerOrangC' => $sumboxorangrtgc,
                                    'RtgBoxPerOrangD' => $sumboxorangrtgd,

                                    'OverBoxA' => $overboxqcca,
                                    'OverBoxB' => $overboxqccb,
                                    'OverBoxC' => $overboxqccc,
                                    'OverBoxD' => $overboxqccd,

                                    'RtgOverBoxA' => $overboxrtga,
                                    'RtgOverBoxB' => $overboxrtgb,
                                    'RtgOverBoxC' => $overboxrtgc,
                                    'RtgOverBoxD' => $overboxrtgd,


                                    'incentiveparam'=>$param                                    

                                    )
                            );

    }

    public function saveStep3(Request $request){

        $incentiveparam = $request->get('incentiveparam');
        $overboxqccA = $request->get('OverBoxA');
        $overboxqccB = $request->get('OverBoxB');
        $overboxqccC = $request->get('OverBoxC');
        $overboxqccD = $request->get('OverBoxD');
        $overboxrtgA = $request->get('RtgOverBoxA');
        $overboxrtgB = $request->get('RtgOverBoxB');
        $overboxrtgC = $request->get('RtgOverBoxC');
        $overboxrtgD = $request->get('RtgOverBoxD');

        // dd($overboxqccA,$overboxqccB,$overboxqccC,$overboxqccD,$overboxrtgA,$overboxrtgB,$overboxrtgC,$overboxrtgD);

        $array = array($overboxqccA,$overboxqccB,$overboxqccC,$overboxqccD,$overboxrtgA,$overboxrtgB,$overboxrtgC,$overboxrtgD);

        $groupmen = DB::table('tx_box_adjusment_group')->select('BOX_ADJUSMENT_GROUP_ID')->where('INCENTIVE_PARAM_ID',$incentiveparam)->get();        
        $a = 0;
        foreach ($groupmen as $groupmens) {
            
            $update = DB::table('tx_box_adjusment_group')
                        ->where('BOX_ADJUSMENT_GROUP_ID', $groupmens->BOX_ADJUSMENT_GROUP_ID)
                        ->update(['BOX_ADJUSMENT_GROUP_OVER_BOX' => $array[$a]]);
            if ($update) {
                // echo 'ntapsul';
            }else{
                // echo 'gagal';
            }
            
            $a++;    
        }

        $selectBoxEmpquery = "SELECT a.BA_ID,a.EMPLOYEE_NIK, b.EMPLOYEE_NAME, b.EMPLOYEE_SLICE, b.EMPLOYEE_BLOCK, a.BAE_INCENTIVE_EMP_FINAL, 
                                c.POS_NOM_FLAT_DIST_BLOCK_I, (c.POS_NOM_FLAT_DIST_BLOCK_I / c.POS_HEADACOUNT_PER_SLICE) AS INCENTIVE_FLAT,
                                e.GROUP_ID, e.POSITION_ID, a.BAE_OJEKER_BOX, a.BAE_BOX_HANDLE,
                                (CASE WHEN e.GROUP_ID = 8 AND b.EMPLOYEE_SLICE = 1 AND e.INCENTIVE_PARAM_ID = '".$incentiveparam."'
                                     THEN (SELECT bag.BOX_ADJUSMENT_GROUP_PRICE FROM tx_box_adjusment_group bag WHERE bag.GROUP_ID = 8 AND bag.POSITION_ID = 67 AND bag.INCENTIVE_PARAM_ID = '".$incentiveparam."')
                                     WHEN e.GROUP_ID = 9 AND b.EMPLOYEE_SLICE = 1 AND e.INCENTIVE_PARAM_ID = '".$incentiveparam."'
                                     THEN (SELECT bag.BOX_ADJUSMENT_GROUP_PRICE FROM tx_box_adjusment_group bag WHERE bag.GROUP_ID = 9 AND bag.POSITION_ID = 67 AND bag.INCENTIVE_PARAM_ID = '".$incentiveparam."')
                                     WHEN e.GROUP_ID = 10 AND b.EMPLOYEE_SLICE = 1 AND e.INCENTIVE_PARAM_ID = '".$incentiveparam."'
                                     THEN (SELECT bag.BOX_ADJUSMENT_GROUP_PRICE FROM tx_box_adjusment_group bag WHERE bag.GROUP_ID = 10 AND bag.POSITION_ID = 67 AND bag.INCENTIVE_PARAM_ID = '".$incentiveparam."')
                                     WHEN e.GROUP_ID = 11 AND b.EMPLOYEE_SLICE = 1 AND e.INCENTIVE_PARAM_ID = '".$incentiveparam."'
                                     THEN (SELECT bag.BOX_ADJUSMENT_GROUP_PRICE FROM tx_box_adjusment_group bag WHERE bag.GROUP_ID = 11 AND bag.POSITION_ID = 67 AND bag.INCENTIVE_PARAM_ID = '".$incentiveparam."')
                                     
                                     WHEN e.GROUP_ID = 8 AND b.EMPLOYEE_SLICE = 2 AND e.INCENTIVE_PARAM_ID = '".$incentiveparam."'
                                     THEN (SELECT bag.BOX_ADJUSMENT_GROUP_PRICE FROM tx_box_adjusment_group bag WHERE bag.GROUP_ID = 8 AND bag.POSITION_ID = 66 AND bag.INCENTIVE_PARAM_ID = '".$incentiveparam."')
                                     WHEN e.GROUP_ID = 9 AND b.EMPLOYEE_SLICE = 2 AND e.INCENTIVE_PARAM_ID = '".$incentiveparam."'
                                     THEN (SELECT bag.BOX_ADJUSMENT_GROUP_PRICE FROM tx_box_adjusment_group bag WHERE bag.GROUP_ID = 9 AND bag.POSITION_ID = 66 AND bag.INCENTIVE_PARAM_ID = '".$incentiveparam."')
                                     WHEN e.GROUP_ID = 10 AND b.EMPLOYEE_SLICE = 2 AND e.INCENTIVE_PARAM_ID = '".$incentiveparam."'
                                     THEN (SELECT bag.BOX_ADJUSMENT_GROUP_PRICE FROM tx_box_adjusment_group bag WHERE bag.GROUP_ID = 10 AND bag.POSITION_ID = 66 AND bag.INCENTIVE_PARAM_ID = '".$incentiveparam."')
                                     WHEN e.GROUP_ID = 11 AND b.EMPLOYEE_SLICE = 2 AND e.INCENTIVE_PARAM_ID = '".$incentiveparam."'
                                     THEN (SELECT bag.BOX_ADJUSMENT_GROUP_PRICE FROM tx_box_adjusment_group bag WHERE bag.GROUP_ID = 11 AND bag.POSITION_ID = 66 AND bag.INCENTIVE_PARAM_ID = '".$incentiveparam."')
                                END) AS HARGA_PER_BOX_OJEKER, e.BOX_ADJUSMENT_GROUP_PRICE AS HARGA_PER_BOX, e.BOX_ADJUSMENT_GROUP_VALUE, e.BOX_ADJUSMENT_GROUP_INCENTIVE
                                FROM tx_box_adjusment_emp a
                                LEFT JOIN tm_employee b ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK AND a.INCENTIVE_PARAM_ID = '".$incentiveparam."' AND b.EMPLOYEE_STATUS = 1
                                LEFT JOIN tm_position d ON b.EMPLOYEE_POS = d.POSITION_ID
                                LEFT JOIN tx_portion_of_slice c ON b.EMPLOYEE_BLOCK = c.BLOCK_ID AND b.EMPLOYEE_SLICE = c.SLICE_ID
                                LEFT JOIN tx_box_adjusment_group e ON e.BOX_ADJUSMENT_GROUP_ID = a.BOX_ADJUSMENT_GROUP_ID
                                WHERE c.INCENTIVE_PARAM_ID = '".$incentiveparam."'
                                ORDER BY b.EMPLOYEE_BLOCK, d.POSITION_ID";

        $selectBoxEmp = DB::select($selectBoxEmpquery);

        foreach ($selectBoxEmp as $selectBoxEmps) {

            $incentiveflat = $selectBoxEmps->INCENTIVE_FLAT;
            $boxhandle = $selectBoxEmps->BAE_BOX_HANDLE;
            $boxgroup = $selectBoxEmps->BOX_ADJUSMENT_GROUP_VALUE;
            $incentiveperunit = $selectBoxEmps->BOX_ADJUSMENT_GROUP_INCENTIVE;

            $incentiveboxemp = $boxhandle / $boxgroup * $incentiveperunit; 

            $boxhandleojek = $selectBoxEmps->BAE_OJEKER_BOX;
            if ($boxhandleojek == 0) {
                $hargaboxojek = 0;
            } else {
                $hargaboxojek = $selectBoxEmps->HARGA_PER_BOX_OJEKER;
            }
            $incentiveboxojek = $boxhandleojek * $hargaboxojek;

            // $incentivefinal = round($selectBoxEmps->INCENTIVE_FLAT + $incentiveboxemp + $incentiveboxojek);

            $updateIncentiveFinal = DB::table('tx_box_adjusment_emp')
                                        ->where('BA_ID',$selectBoxEmps->BA_ID)
                                        ->update([
                                                // 'BAE_INCENTIVE_EMP_FINAL' => $incentivefinal,
                                                'BAE_INCENTIVE_EMP' => $incentiveboxemp,
                                                'BAE_OJEKER_BOX' => $boxhandleojek,
                                                'BAE_OJEKER_BOX_PRICE' => $hargaboxojek,
                                                'BAE_OJEKER_TOTAL' => $incentiveboxojek
                                                ]);       

            if ($updateIncentiveFinal) {
                // echo 'ntapsul123';
            }else{
                // echo 'gagal123'.'<br>';
            }  
        }

        $setting = AdjustParamModel::select('AP_BA_MIN_BOX_QCC','AP_BA_MIN_BOX_RTG','AP_PENALTY')->where('ADJUST_PARAM_ID',1)->first();
        $minqcc = $setting->AP_BA_MIN_BOX_QCC;
        $minrtg = $setting->AP_BA_MIN_BOX_RTG;
        $penalty = $setting->AP_PENALTY;

        //CARI SUM UPPER DAN UNDER THRESHOLD

        if ($penalty == 2) {
            //QCC
            $sumUpperThreQCC_query = "SELECT SUM(BAE_INCENTIVE_EMP) + SUM(BAE_OJEKER_TOTAL) AS Upper_threshold_qcc
                                        FROM tx_box_adjusment_emp a 
                                        LEFT JOIN tm_employee b ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK AND b.EMPLOYEE_STATUS = 1
                                        WHERE a.INCENTIVE_PARAM_ID = '".$incentiveparam."' AND b.EMPLOYEE_BLOCK = 1 AND b.EMPLOYEE_SLICE = 1 AND BAE_BOX_HANDLE >= '".$minqcc."'"; 
            $sumUpperThreQCC = DB::select($sumUpperThreQCC_query);
            $TotalUpperQCC = $sumUpperThreQCC[0]->Upper_threshold_qcc;
            

            $sumUnderThreQCC_query = "SELECT SUM(BAE_INCENTIVE_EMP) + SUM(BAE_OJEKER_TOTAL) AS Under_threshold_qcc
                                        FROM tx_box_adjusment_emp a 
                                        LEFT JOIN tm_employee b ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK AND b.EMPLOYEE_STATUS = 1
                                        WHERE a.INCENTIVE_PARAM_ID = '".$incentiveparam."' AND b.EMPLOYEE_BLOCK = 1 AND b.EMPLOYEE_SLICE = 1 AND BAE_BOX_HANDLE < '".$minqcc."'"; 
            $sumUnderThreQCC = DB::select($sumUnderThreQCC_query);
            $TotalUnderQCC = $sumUnderThreQCC[0]->Under_threshold_qcc;

            //RTG
            $sumUpperThreRTG_query = "SELECT SUM(BAE_INCENTIVE_EMP) + SUM(BAE_OJEKER_TOTAL) AS Upper_threshold_rtg
                                        FROM tx_box_adjusment_emp a 
                                        LEFT JOIN tm_employee b ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK AND b.EMPLOYEE_STATUS = 1
                                        WHERE a.INCENTIVE_PARAM_ID = '".$incentiveparam."' AND b.EMPLOYEE_BLOCK = 1 AND b.EMPLOYEE_SLICE = 2 AND BAE_BOX_HANDLE >= '".$minrtg."'"; 
            $sumUpperThreRTG = DB::select($sumUpperThreRTG_query);
            $TotalUpperRTG = $sumUpperThreRTG[0]->Upper_threshold_rtg;

            $sumUnderThreRTG_query = "SELECT SUM(BAE_INCENTIVE_EMP) + SUM(BAE_OJEKER_TOTAL) AS Under_threshold_rtg
                                        FROM tx_box_adjusment_emp a 
                                        LEFT JOIN tm_employee b ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK AND b.EMPLOYEE_STATUS = 1
                                        WHERE a.INCENTIVE_PARAM_ID = '".$incentiveparam."' AND b.EMPLOYEE_BLOCK = 1 AND b.EMPLOYEE_SLICE = 2 AND BAE_BOX_HANDLE < '".$minrtg."'"; 
            $sumUnderThreRTG = DB::select($sumUnderThreRTG_query);
            $TotalUnderRTG = $sumUnderThreRTG[0]->Under_threshold_rtg;

        } else {
            //QCC
            $sumFLATQCC_query = "SELECT SUM(c.POS_NOM_FLAT_DIST_BLOCK_I / c.POS_HEADACOUNT_PER_SLICE) AS Flat
                                        FROM tx_box_adjusment_emp a
                                        LEFT JOIN tm_employee b ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK AND a.INCENTIVE_PARAM_ID = '".$incentiveparam."' AND b.EMPLOYEE_STATUS = 1
                                        LEFT JOIN tx_portion_of_slice c ON b.EMPLOYEE_BLOCK = c.BLOCK_ID AND b.EMPLOYEE_SLICE = c.SLICE_ID
                                        WHERE c.INCENTIVE_PARAM_ID = '".$incentiveparam."' AND b.EMPLOYEE_BLOCK = 1 AND b.EMPLOYEE_SLICE = 1 AND a.BAE_BOX_HANDLE < '".$minqcc."'"; 

            $sumFLATQCC = DB::select($sumFLATQCC_query);
            $TotalFlatQCC = $sumFLATQCC[0]->Flat;
            

            $sumKaryawanQCC_Query = "SELECT COUNT(BA_ID) AS count_qcc
                                        FROM tx_box_adjusment_emp a
                                        LEFT JOIN tm_employee b ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK AND a.INCENTIVE_PARAM_ID = '".$incentiveparam."' AND b.EMPLOYEE_STATUS = 1
                                        LEFT JOIN tx_portion_of_slice c ON b.EMPLOYEE_BLOCK = c.BLOCK_ID AND b.EMPLOYEE_SLICE = c.SLICE_ID
                                        WHERE c.INCENTIVE_PARAM_ID = '".$incentiveparam."' AND b.EMPLOYEE_BLOCK = 1 AND b.EMPLOYEE_SLICE = 1 AND a.BAE_BOX_HANDLE >= '".$minqcc."'"; 
            $sumKaryawanQCC = DB::select($sumKaryawanQCC_Query);
            $TotalKaryawanQCC = $sumKaryawanQCC[0]->count_qcc;

            //RTG
            $sumFLATRTG_query = "SELECT SUM(c.POS_NOM_FLAT_DIST_BLOCK_I / c.POS_HEADACOUNT_PER_SLICE) AS Flat
                                        FROM tx_box_adjusment_emp a
                                        LEFT JOIN tm_employee b ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK AND a.INCENTIVE_PARAM_ID = '".$incentiveparam."' AND b.EMPLOYEE_STATUS = 1
                                        LEFT JOIN tx_portion_of_slice c ON b.EMPLOYEE_BLOCK = c.BLOCK_ID AND b.EMPLOYEE_SLICE = c.SLICE_ID
                                        WHERE c.INCENTIVE_PARAM_ID = '".$incentiveparam."' AND b.EMPLOYEE_BLOCK = 1 AND b.EMPLOYEE_SLICE = 2 AND a.BAE_BOX_HANDLE < '".$minrtg."'"; 

            $sumFLATRTG = DB::select($sumFLATRTG_query);
            $TotalFlatRTG = $sumFLATRTG[0]->Flat;
            

            $sumKaryawanRTG_Query = "SELECT COUNT(BA_ID) AS count_rtg
                                        FROM tx_box_adjusment_emp a
                                        LEFT JOIN tm_employee b ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK AND a.INCENTIVE_PARAM_ID = '".$incentiveparam."' AND b.EMPLOYEE_STATUS = 1
                                        LEFT JOIN tx_portion_of_slice c ON b.EMPLOYEE_BLOCK = c.BLOCK_ID AND b.EMPLOYEE_SLICE = c.SLICE_ID
                                        WHERE c.INCENTIVE_PARAM_ID = '".$incentiveparam."' AND b.EMPLOYEE_BLOCK = 1 AND b.EMPLOYEE_SLICE = 2 AND a.BAE_BOX_HANDLE >= '".$minrtg."'"; 
            $sumKaryawanRTG = DB::select($sumKaryawanRTG_Query);
            $TotalKaryawanRTG = $sumKaryawanRTG[0]->count_rtg;


        }  
        

        $box_emp_qcc = BoxAdjusmentEmpModel::select('*')
                                            ->leftjoin('tm_employee','tx_box_adjusment_emp.EMPLOYEE_NIK','=','tm_employee.EMPLOYEE_NIK')
                                            ->where('tx_box_adjusment_emp.INCENTIVE_PARAM_ID',$incentiveparam)
                                            // ->where('tm_employee.EMPLOYEE_POS',66)
                                            ->where('tm_employee.EMPLOYEE_SLICE',1)
                                            ->where('tm_employee.EMPLOYEE_BLOCK',1)
                                            ->where('tm_employee.EMPLOYEE_STATUS',1)
                                            ->get();

        $box_emp_rtg = BoxAdjusmentEmpModel::select('*')
                                            ->leftjoin('tm_employee','tx_box_adjusment_emp.EMPLOYEE_NIK','=','tm_employee.EMPLOYEE_NIK')
                                            ->where('tx_box_adjusment_emp.INCENTIVE_PARAM_ID',$incentiveparam)
                                            // ->where('tm_employee.EMPLOYEE_POS',67)
                                            ->where('tm_employee.EMPLOYEE_SLICE',2)
                                            ->where('tm_employee.EMPLOYEE_BLOCK',1)
                                            ->where('tm_employee.EMPLOYEE_STATUS',1)
                                            ->get();


        //UPDATE QCC
        foreach ($box_emp_qcc as $box_emp_qccs) {
            $BoxPlusOjekerQCC = $box_emp_qccs->BAE_INCENTIVE_EMP + $box_emp_qccs->BAE_OJEKER_TOTAL; 
            if ($box_emp_qccs->BAE_BOX_HANDLE < $minqcc) {
                $reDistqcc = 0;
                $incentivefinalboxqcc = $BoxPlusOjekerQCC;
            }else{
                if ($penalty == 2) {
                    $reDistqcc = $BoxPlusOjekerQCC / $TotalUpperQCC * $TotalUnderQCC;
                    $incentivefinalboxqcc = $BoxPlusOjekerQCC + $reDistqcc;
                } else {
                    $reDistqcc = $TotalFlatQCC / $TotalKaryawanQCC;
                    $incentivefinalboxqcc = $BoxPlusOjekerQCC + $reDistqcc;
                }
            }

            $updateQCCFinalBox = DB::table('tx_box_adjusment_emp')
                                        ->where('BA_ID',$box_emp_qccs->BA_ID)
                                        ->update([
                                                'BAE_RE_DISTRIBUTE' => $reDistqcc,
                                                'BAE_INCENTIVE_EMP_FINAL' => $incentivefinalboxqcc
                                                ]); 
        }

        //UPDATE RTG
         foreach ($box_emp_rtg as $box_emp_rtgs) {
            $BoxPlusOjekerRTG = $box_emp_rtgs->BAE_INCENTIVE_EMP + $box_emp_rtgs->BAE_OJEKER_TOTAL; 
            if ($box_emp_rtgs->BAE_BOX_HANDLE < $minrtg) {
                $reDistrtg = 0;
                $incentivefinalboxrtg = $BoxPlusOjekerRTG;
            }else{
                if ($penalty == 2) {
                    $reDistrtg = $BoxPlusOjekerRTG / $TotalUpperRTG * $TotalUnderRTG;
                    $incentivefinalboxrtg = $BoxPlusOjekerRTG + $reDistrtg;
                } else {
                    $reDistrtg = $TotalFlatRTG / $TotalKaryawanRTG;
                    $incentivefinalboxrtg = $BoxPlusOjekerRTG + $reDistrtg;
                }
            }

            $updateRTGFinalBox = DB::table('tx_box_adjusment_emp')
                                        ->where('BA_ID',$box_emp_rtgs->BA_ID)
                                        ->update([
                                                'BAE_RE_DISTRIBUTE' => $reDistrtg,
                                                'BAE_INCENTIVE_EMP_FINAL' => $incentivefinalboxrtg
                                                ]); 
        }

        //END UPDATE DIST

        $cektemp = TempInsentifModel::select('INCENTIVE_PARAM_ID')->where('INCENTIVE_PARAM_ID','<>',$incentiveparam)->count();
        
        if ($cektemp > 0) {
            $truncate = DB::table('tx_temp_incentive')->truncate();
        }     

        $selectFlat = "SELECT a.EMPLOYEE_NIK, (c.POS_NOM_FLAT_DIST_BLOCK_I / c.POS_HEADACOUNT_PER_SLICE) AS INCENTIVE_FLAT,
                        b.BAE_OJEKER_TOTAL, b.BAE_OJEKER_BOX, b.BAE_OJEKER_BOX_PRICE, e.BOX_ADJUSMENT_GROUP_VALUE, e.BOX_ADJUSMENT_GROUP_INCENTIVE, b.BAE_INCENTIVE_EMP,
                        b.BAE_RE_DISTRIBUTE, b.BAE_INCENTIVE_EMP_FINAL, b.BAE_BOX_HANDLE, a.EMPLOYEE_POS, a.EMPLOYEE_BLOCK, a.EMPLOYEE_SLICE
                        FROM tm_employee a
                        LEFT JOIN tm_position d ON a.EMPLOYEE_POS = d.POSITION_ID
                        LEFT JOIN tx_box_adjusment_emp b ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK AND b.INCENTIVE_PARAM_ID = '".$incentiveparam."'
                        LEFT JOIN tx_portion_of_slice c ON a.EMPLOYEE_BLOCK = c.BLOCK_ID AND a.EMPLOYEE_SLICE = c.SLICE_ID
                        LEFT JOIN tx_box_adjusment_group e ON e.BOX_ADJUSMENT_GROUP_ID = b.BOX_ADJUSMENT_GROUP_ID
                        WHERE c.INCENTIVE_PARAM_ID = '".$incentiveparam."' AND a.EMPLOYEE_STATUS = 1
                        ORDER BY a.EMPLOYEE_BLOCK, d.POSITION_ID
                        ";

        $selectFlats = DB::select($selectFlat);

        foreach ($selectFlats as $selectFlats123) {

            if ($selectFlats123->EMPLOYEE_SLICE == 1 and $selectFlats123->EMPLOYEE_BLOCK == 1) {
                if ($selectFlats123->BAE_BOX_HANDLE < $minqcc) {
                    if ($penalty == 2) {
                        $totalincentive = $selectFlats123->INCENTIVE_FLAT;
                    } else {
                        $totalincentive = $selectFlats123->BAE_INCENTIVE_EMP_FINAL;
                    }
                }else{
                    $totalincentive = $selectFlats123->INCENTIVE_FLAT + $selectFlats123->BAE_INCENTIVE_EMP_FINAL;    
                }
            }elseif ($selectFlats123->EMPLOYEE_SLICE == 2 and $selectFlats123->EMPLOYEE_BLOCK == 1) {
                if ($selectFlats123->BAE_BOX_HANDLE < $minrtg) {
                    if ($penalty == 2) {
                        $totalincentive = $selectFlats123->INCENTIVE_FLAT;
                    } else {
                        $totalincentive = $selectFlats123->BAE_INCENTIVE_EMP_FINAL;
                    }
                }else{
                    $totalincentive = $selectFlats123->INCENTIVE_FLAT + $selectFlats123->BAE_INCENTIVE_EMP_FINAL;    
                }
            }else{
                $totalincentive = $selectFlats123->INCENTIVE_FLAT + $selectFlats123->BAE_INCENTIVE_EMP_FINAL;
            }

            $insertFlat =  TempInsentifModel::updateOrCreate([
                                                        'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                        'EMPLOYEE_NIK' => $selectFlats123->EMPLOYEE_NIK
                                                        ],
                                                        [
                                                        'TEMP_INCENTIVE_FLAT' => $selectFlats123->INCENTIVE_FLAT,
                                                        'TEMP_INCENTIVE_BOX' => $selectFlats123->BAE_INCENTIVE_EMP,
                                                        'TEMP_INCENTIVE_OJEKER' => $selectFlats123->BAE_OJEKER_TOTAL,
                                                        'TEMP_INCENTIVE_THRESHOLD' => $selectFlats123->BAE_RE_DISTRIBUTE,
                                                        'TEMP_INCENTIVE_TOTAL' => $totalincentive
                                                        ]
                                                     );
            if ($insertFlat->save()) {
                // echo 'mantap';
            } else {
                // echo 'sayang sekali';
            }
        }

        $updateParam = DB::table('tx_incentive_param')
                        ->where('INCENTIVE_PARAM_ID', $incentiveparam)
                        ->update(['INCENTIVE_PARAM_STATUS' => 3]);

        return Response::json(array('success' => true, 'incentiveparam' => $incentiveparam));

    }

    public function saveStep4(Request $request){
        
        $incentiveparam = $request->get('incentiveparam');
        
        // $select_temp = TempInsentifModel::select('*')->where('INCENTIVE_PARAM_ID',$incentiveparam)->get();
        $select_temp = TempInsentifModel::select('tm_employee.EMPLOYEE_NIK','tm_employee.EMPLOYEE_NAME','tm_employee.EMPLOYEE_SLICE',
                                                  'tx_temp_incentive.TEMP_INCENTIVE_TOTAL','tx_temp_incentive.TEMP_INCENTIVE_PERCENT_CUT',
                                                  'tx_temp_incentive.TEMP_INCENTIVE_COST_CUT'
                                                 )
                                         // ->leftjoin('tm_employee','tm_employee.EMPLOYEE_NIK','=','tx_temp_incentive.EMPLOYEE_NIK')
                                         ->leftjoin('tm_employee',function($join){
                                            $join->on('tm_employee.EMPLOYEE_NIK','=','tx_temp_incentive.EMPLOYEE_NIK')                                                 
                                                 ->where('tm_employee.EMPLOYEE_STATUS','=',1);
                                            })
                                         ->where('tx_temp_incentive.INCENTIVE_PARAM_ID',$incentiveparam)                                         
                                         ->orderby('tm_employee.EMPLOYEE_SLICE','ASC')
                                         ->get();        

        foreach ($select_temp as $data) {
            $persen_potongan = $data->TEMP_INCENTIVE_PERCENT_CUT; 
            $final = $data->TEMP_INCENTIVE_TOTAL;
            if ($persen_potongan == null OR $persen_potongan == 0.00) {
                $cost_potongan = 0.000;
            } else {
                $cost_potongan = $persen_potongan / 100 * $final;
            }

            $updateTemp =  TempInsentifModel::updateOrCreate([
                                                                'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                                'EMPLOYEE_NIK' => $data->EMPLOYEE_NIK
                                                                ],
                                                                [                                                                
                                                                'TEMP_INCENTIVE_COST_CUT' => $cost_potongan
                                                                ]
                                                             );    
            $updateTemp->save();

        }        

        // $select_temp2 = TempInsentifModel::select('tm_employee.EMPLOYEE_NIK','tm_employee.EMPLOYEE_NAME','tm_employee.EMPLOYEE_SLICE',
        //                                           'tx_temp_incentive.TEMP_INCENTIVE_TOTAL','tx_temp_incentive.TEMP_INCENTIVE_PERCENT_CUT',
        //                                           'tx_temp_incentive.TEMP_INCENTIVE_COST_CUT'
        //                                          )
        //                                  ->leftjoin('tm_employee','tm_employee.EMPLOYEE_NIK','=','tx_temp_incentive.EMPLOYEE_NIK')
        //                                  ->where('tx_temp_incentive.INCENTIVE_PARAM_ID',$incentiveparam)
        //                                  ->orderby('tm_employee.EMPLOYEE_SLICE','ASC')
        //                                  ->get();

        foreach ($select_temp as $data2) {

            // ----- KALO PER SLICE -----
            // $selectCost = "SELECT a.INCENTIVE_PARAM_ID, b.EMPLOYEE_SLICE, COUNT(b.EMPLOYEE_NAME) AS count_slice, 
            //                 (
            //                     SELECT COUNT(*)
            //                     FROM tx_temp_incentive ti 
            //                     LEFT JOIN tm_employee te ON ti.EMPLOYEE_NIK = te.EMPLOYEE_NIK
            //                     WHERE (ti.TEMP_INCENTIVE_PERCENT_CUT IS NULL OR ti.TEMP_INCENTIVE_PERCENT_CUT = 0.00) 
            //                     AND te.EMPLOYEE_SLICE = b.EMPLOYEE_SLICE
            //                 ) AS count_tidak_kepotong,
            //                 SUM(a.TEMP_INCENTIVE_COST_CUT) AS sum_potongan, 
            //                 ((SUM(a.TEMP_INCENTIVE_COST_CUT)) / (
            //                     SELECT COUNT(*)
            //                     FROM tx_temp_incentive ti 
            //                     LEFT JOIN tm_employee te ON ti.EMPLOYEE_NIK = te.EMPLOYEE_NIK
            //                     WHERE (ti.TEMP_INCENTIVE_PERCENT_CUT IS NULL OR ti.TEMP_INCENTIVE_PERCENT_CUT = 0.00) 
            //                     AND te.EMPLOYEE_SLICE = b.EMPLOYEE_SLICE
            //                 )) AS tambahan_yg_tidakkepotong
            //                 FROM tx_temp_incentive a 
            //                 LEFT JOIN tm_employee b ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK
            //                 WHERE a.INCENTIVE_PARAM_ID = '".$incentiveparam."' AND b.EMPLOYEE_SLICE = '".$data2->EMPLOYEE_SLICE."'
            //                 GROUP BY b.EMPLOYEE_SLICE, a.INCENTIVE_PARAM_ID
            //                 ORDER BY b.EMPLOYEE_SLICE ASC
            //               ";

            // $selectCost = "SELECT a.INCENTIVE_PARAM_ID, b.EMPLOYEE_SLICE, COUNT(b.EMPLOYEE_NAME) AS count_slice, 
            //                 (
            //                     SELECT COUNT(*)
            //                     FROM tx_temp_incentive ti 
            //                     LEFT JOIN tm_employee te ON ti.EMPLOYEE_NIK = te.EMPLOYEE_NIK
            //                     WHERE (ti.TEMP_INCENTIVE_PERCENT_CUT IS NULL OR ti.TEMP_INCENTIVE_PERCENT_CUT = 0.00) 
            //                     AND te.EMPLOYEE_SLICE = b.EMPLOYEE_SLICE
            //                 ) AS count_tidak_kepotong,
            //                 SUM(a.TEMP_INCENTIVE_COST_CUT) AS sum_potongan, 
            //                 ((SUM(a.TEMP_INCENTIVE_COST_CUT)) / (
            //                     SELECT COUNT(*)
            //                     FROM tx_temp_incentive ti 
            //                     LEFT JOIN tm_employee te ON ti.EMPLOYEE_NIK = te.EMPLOYEE_NIK
            //                     WHERE (ti.TEMP_INCENTIVE_PERCENT_CUT IS NULL OR ti.TEMP_INCENTIVE_PERCENT_CUT = 0.00) 
            //                     AND te.EMPLOYEE_SLICE = b.EMPLOYEE_SLICE
            //                 )) AS tambahan_yg_tidakkepotong
            //                 FROM tx_temp_incentive a 
            //                 LEFT JOIN tm_employee b ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK
            //                 WHERE a.INCENTIVE_PARAM_ID = '".$incentiveparam."' AND b.EMPLOYEE_SLICE = '".$data2->EMPLOYEE_SLICE."'
            //                 GROUP BY b.EMPLOYEE_SLICE, a.INCENTIVE_PARAM_ID
            //                 ORDER BY b.EMPLOYEE_SLICE ASC
            //               ";

            // $selectCosts = DB::select($selectCost);            

            // $tambahan = $selectCosts[0]->tambahan_yg_tidakkepotong;
            // $potongan = $data2->TEMP_INCENTIVE_COST_CUT;

            // if ($data2->TEMP_INCENTIVE_PERCENT_CUT > 0) {
            //     $simpan_tambahan = 0;
            //     $finalfinal = $data2->TEMP_INCENTIVE_TOTAL - $potongan;
            // } else {
            //     $simpan_tambahan = $tambahan;
            //     $finalfinal = $data2->TEMP_INCENTIVE_TOTAL + $tambahan;
            // }

            // END KALO PER SLICE

            // Dibagi rata

            $total_potongan = TempInsentifModel::select(DB::raw('SUM(TEMP_INCENTIVE_COST_CUT) as total1'))->where('TEMP_INCENTIVE_PERCENT_CUT','>',0)->get();
            $total_incentive_tidakkepotong = TempInsentifModel::select(DB::raw('SUM(TEMP_INCENTIVE_TOTAL) as total2'))->where('TEMP_INCENTIVE_PERCENT_CUT',0)->get();

            
            $total_potongans = $total_potongan[0]->total1;
            $total_incentive_tidakkepotongs = $total_incentive_tidakkepotong[0]->total2;

            // dd($total_potongans, $total_incentive_tidakkepotongs);

            $tambahan = $data2->TEMP_INCENTIVE_TOTAL / $total_incentive_tidakkepotongs * $total_potongans;
            $potongan = $data2->TEMP_INCENTIVE_COST_CUT;

            if ($data2->TEMP_INCENTIVE_PERCENT_CUT > 0) {
                $simpan_tambahan = 0;
                $finalfinal = $data2->TEMP_INCENTIVE_TOTAL - $potongan;
            } else {
                $simpan_tambahan = $tambahan;
                $finalfinal = $data2->TEMP_INCENTIVE_TOTAL + $tambahan;
            }            

            $updateTemp2 =  TempInsentifModel::updateOrCreate([
                                                                'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                                'EMPLOYEE_NIK' => $data2->EMPLOYEE_NIK
                                                                ],
                                                                [                                                                
                                                                'TEMP_INCENTIVE_DIST_CUT' => $simpan_tambahan,    
                                                                'TEMP_INCENTIVE_TOTAL_AFTER_CUT' => $finalfinal
                                                                ]
                                                             );    
            $updateTemp2->save();
            
        }

        // $updateParam = DB::table('tx_incentive_param')
        //                 ->where('INCENTIVE_PARAM_ID', $incentiveparam)
        //                 ->update(['INCENTIVE_PARAM_STATUS' => 4]);    

        $updateParam = DB::table('tx_incentive_param')
                        ->where('INCENTIVE_PARAM_ID', $incentiveparam)
                        ->update(['INCENTIVE_PARAM_STATUS' => 5]);        

        return Response::json(array('success' => true, 'incentiveparam' => $incentiveparam)); 
    }

    public function saveStep5($incentiveparam){

        $data = EmployeeModel::select('tm_employee.EMPLOYEE_NIK','tm_employee.EMPLOYEE_NAME','tm_employee.EMPLOYEE_POS', 'tm_employee.EMPLOYEE_GROUP', 'tm_employee.EMPLOYEE_SHIFT'
                                        , 'tm_employee.EMPLOYEE_BLOCK', 'tm_employee.EMPLOYEE_SLICE','tm_employee.EMPLOYEE_DIV', 'tm_employee.EMPLOYEE_GRADE',
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_FLAT IS NULL THEN "0" ELSE tx_temp_incentive.TEMP_INCENTIVE_FLAT END as TEMP_INCENTIVE_FLAT'),
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_TOTAL IS NULL THEN "0" ELSE tx_temp_incentive.TEMP_INCENTIVE_TOTAL END as TEMP_INCENTIVE_TOTAL'),
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_OJEKER IS NULL THEN "0" ELSE tx_temp_incentive.TEMP_INCENTIVE_OJEKER END as TEMP_INCENTIVE_OJEKER'),
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_THRESHOLD IS NULL THEN "0" ELSE tx_temp_incentive.TEMP_INCENTIVE_THRESHOLD END as TEMP_INCENTIVE_THRESHOLD'),
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_BOX IS NULL THEN "0" ELSE tx_temp_incentive.TEMP_INCENTIVE_BOX END as TEMP_INCENTIVE_BOX'),
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_PERCENT_CUT IS NULL THEN "0" ELSE tx_temp_incentive.TEMP_INCENTIVE_PERCENT_CUT END as TEMP_INCENTIVE_PERCENT_CUT'),
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_COST_CUT IS NULL THEN "0" ELSE tx_temp_incentive.TEMP_INCENTIVE_COST_CUT END as TEMP_INCENTIVE_COST_CUT'),
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_DIST_CUT IS NULL THEN "0" ELSE tx_temp_incentive.TEMP_INCENTIVE_DIST_CUT END as TEMP_INCENTIVE_DIST_CUT'),
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_TOTAL_AFTER_CUT IS NULL THEN "0" ELSE tx_temp_incentive.TEMP_INCENTIVE_TOTAL_AFTER_CUT END as TEMP_INCENTIVE_TOTAL_AFTER_CUT')
                                      )
                            ->leftjoin('tx_temp_incentive',function($join) use ($incentiveparam){
                                $join->on('tm_employee.EMPLOYEE_NIK','=','tx_temp_incentive.EMPLOYEE_NIK')
                                     ->where('tx_temp_incentive.INCENTIVE_PARAM_ID','=',$incentiveparam);
                            })
                            ->where('tm_employee.EMPLOYEE_STATUS','=',1)
                            ->orderby('tm_employee.EMPLOYEE_BLOCK','ASC')
                            ->orderby('tm_employee.EMPLOYEE_SLICE','ASC')
                            ->get();

        foreach ($data as $tempInsentif) {

            $PostingIncentive = PostingIncentiveModel::updateOrCreate([
                                                        'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                        'POSTING_INCENTIVE_NIK' => $tempInsentif->EMPLOYEE_NIK
                                                        ],
                                                        [
                                                        'POSTING_INCENTIVE_NAME' => $tempInsentif->EMPLOYEE_NAME,
                                                        'POSTING_INCENTIVE_POS' => $tempInsentif->EMPLOYEE_POS,
                                                        'POSTING_INCENTIVE_GROUP' => $tempInsentif->EMPLOYEE_GROUP,
                                                        'POSTING_INCENTIVE_SHIFT' => $tempInsentif->EMPLOYEE_SHIFT,
                                                        'POSTING_INCENTIVE_BLOCK' => $tempInsentif->EMPLOYEE_BLOCK,
                                                        'POSTING_INCENTIVE_SLICE' => $tempInsentif->EMPLOYEE_SLICE,
                                                        'POSTING_INCENTIVE_DIV' => $tempInsentif->EMPLOYEE_DIV,
                                                        'POSTING_INCENTIVE_GRADE' => $tempInsentif->EMPLOYEE_GRADE,
                                                        'POSTING_INCENTIVE_FLAT' => $tempInsentif->TEMP_INCENTIVE_FLAT,
                                                        'POSTING_INCENTIVE_BOX' => $tempInsentif->TEMP_INCENTIVE_BOX,
                                                        'POSTING_INCENTIVE_OJEKER' => $tempInsentif->TEMP_INCENTIVE_OJEKER,
                                                        'POSTING_INCENTIVE_THRESHOLD' => $tempInsentif->TEMP_INCENTIVE_THRESHOLD,
                                                        'POSTING_INCENTIVE_FINAL' => $tempInsentif->TEMP_INCENTIVE_TOTAL,
                                                        'POSTING_INCENTIVE_PERCENT_CUT' => $tempInsentif->TEMP_INCENTIVE_PERCENT_CUT,
                                                        'POSTING_INCENTIVE_COST_CUT' => $tempInsentif->TEMP_INCENTIVE_COST_CUT,
                                                        'POSTING_INCENTIVE_DIST_CUT' => $tempInsentif->TEMP_INCENTIVE_DIST_CUT,
                                                        'POSTING_INCENTIVE_FINAL_AFTER_CUT' => $tempInsentif->TEMP_INCENTIVE_TOTAL_AFTER_CUT,
                                                        ]
                                                     );
            $PostingIncentive->save();

        }

        $selectSetting = DB::table('tr_adjust_param')->select('*')->where('ADJUST_PARAM_ID',1)->first();
        $updatePost = PostingIncentiveModel::where('INCENTIVE_PARAM_ID', $incentiveparam)
                        ->update([
                                    'POSTING_INCENTIVE_POB_DIFF_BLOCK_I' => $selectSetting->AP_POB_DIFF_BLOCK_I,
                                    'POSTING_INCENTIVE_POB_DIFF_BLOCK_II' => $selectSetting->AP_POB_DIFF_BLOCK_II,
                                    'POSTING_INCENTIVE_POB_DIFF_BLOCK_III' => $selectSetting->AP_POB_DIFF_BLOCK_III,
                                    'POSTING_INCENTIVE_POB_CUV_BLOCK_I' => $selectSetting->AP_POB_CUV_BLOCK_I,
                                    'POSTING_INCENTIVE_DIST_FLAT_BLOCK_I' => $selectSetting->AP_POS_DIST_FLAT_BLOCK_I,
                                    'POSTING_INCENTIVE_DIFF_SLICE_BLOCK_I' => $selectSetting->AP_POS_DIFF_SLICE_BLOCK_I,
                                    'POSTING_INCENTIVE_DIFF_SLICE_BLOCK_IIA' => $selectSetting->AP_POS_DIFF_SLICE_BLOCK_IIA,
                                    'POSTING_INCENTIVE_DIFF_SLICE_BLOCK_IIB' => $selectSetting->AP_POS_DIFF_SLICE_BLOCK_IIB,
                                    'POSTING_INCENTIVE_DIFF_SLICE_BLOCK_III' => $selectSetting->AP_POS_DIFF_SLICE_BLOCK_III,
                                    'POSTING_INCENTIVE_POS_CUV_BLOCK_I' => $selectSetting->AP_POS_CUV_BLOCK_I,
                                    'POSTING_INCENTIVE_POS_CUV_BLOCK_II' => $selectSetting->AP_POS_CUV_BLOCK_II,
                                    'POSTING_INCENTIVE_POS_CUV_BLOCK_III' => $selectSetting->AP_POS_CUV_BLOCK_III,
                                    'POSTING_INCENTIVE_BA_MIN_BOX_QCC' => $selectSetting->AP_BA_MIN_BOX_QCC,
                                    'POSTING_INCENTIVE_BA_MIN_BOX_RTG' => $selectSetting->AP_BA_MIN_BOX_RTG
                                ]);
        
        $updateParam = DB::table('tx_incentive_param')
                        ->where('INCENTIVE_PARAM_ID', $incentiveparam)
                        ->update(['INCENTIVE_PARAM_STATUS' => 6]);

        return Response::json(array('success' => true));
    }

    public function getCountBlock1(){
        $count = DB::table('tm_employee')->where('EMPLOYEE_BLOCK',1)->where('EMPLOYEE_STATUS',1)->count();
        return Response::json(array('count'=>$count));
    }

    public function getCountBlock2(){
        $count = DB::table('tm_employee')->where('EMPLOYEE_BLOCK',2)->where('EMPLOYEE_STATUS',1)->count();
        return Response::json(array('count'=>$count));
    }

    public function getCountBlock3(){
        $count = DB::table('tm_employee')->where('EMPLOYEE_BLOCK',3)->where('EMPLOYEE_STATUS',1)->count();
        return Response::json(array('count'=>$count));
    }

    public function getPotonganKar($id){
        $datas = "SELECT a.EMPLOYEE_NIK, a.EMPLOYEE_NAME, a.EMPLOYEE_SLICE,
                    (SELECT SLICE_NAME FROM tr_slice WHERE SLICE_ID = a.EMPLOYEE_SLICE) as SLICE_KAR,
                    CASE WHEN b.TEMP_INCENTIVE_TOTAL IS NULL THEN '0' ELSE FORMAT(ROUND(b.TEMP_INCENTIVE_TOTAL),0) END as TEMP_INCENTIVE_TOTAL,
                    CASE WHEN b.TEMP_INCENTIVE_TOTAL IS NULL THEN '0' ELSE b.TEMP_INCENTIVE_TOTAL END as TEMP_INCENTIVE_TOTAL_BERSIH,
                    CASE WHEN b.TEMP_INCENTIVE_COST_CUT IS NULL THEN '0' ELSE b.TEMP_INCENTIVE_COST_CUT END as TEMP_INCENTIVE_COST_CUT,
                    CASE WHEN b.TEMP_INCENTIVE_COST_CUT IS NULL THEN '0' ELSE ROUND(b.TEMP_INCENTIVE_COST_CUT) END as TEMP_INCENTIVE_COST_CUT_ROUND,
                    (CASE 
                      WHEN b.TEMP_INCENTIVE_PERCENT_CUT IS NULL THEN 0
                      ELSE b.TEMP_INCENTIVE_PERCENT_CUT
                    END) AS TEMP_INCENTIVE_PERCENT_CUT
                    FROM (SELECT EMPLOYEE_NIK, TEMP_INCENTIVE_PERCENT_CUT, TEMP_INCENTIVE_TOTAL, TEMP_INCENTIVE_COST_CUT FROM tx_temp_incentive WHERE INCENTIVE_PARAM_ID = '".$id."') b
                    LEFT JOIN tm_employee AS a
                    ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK AND a.EMPLOYEE_STATUS = 1
                    ORDER BY a.EMPLOYEE_SLICE ASC";

        $data = DB::select($datas);
        
        return response($data);
    }

    public function potonganKarSearch($id){
        $coba = explode('&', $id);
        $incentiveparam = $coba[0];
        $text = $coba[1];
        $datas = "SELECT a.EMPLOYEE_NIK, a.EMPLOYEE_NAME, a.EMPLOYEE_SLICE,
                    (SELECT SLICE_NAME FROM tr_slice WHERE SLICE_ID = a.EMPLOYEE_SLICE) as SLICE_KAR,
                    CASE WHEN b.TEMP_INCENTIVE_TOTAL IS NULL THEN '0' ELSE FORMAT(ROUND(b.TEMP_INCENTIVE_TOTAL),0) END as TEMP_INCENTIVE_TOTAL,
                    CASE WHEN b.TEMP_INCENTIVE_TOTAL IS NULL THEN '0' ELSE b.TEMP_INCENTIVE_TOTAL END as TEMP_INCENTIVE_TOTAL_BERSIH,
                    CASE WHEN b.TEMP_INCENTIVE_COST_CUT IS NULL THEN '0' ELSE b.TEMP_INCENTIVE_COST_CUT END as TEMP_INCENTIVE_COST_CUT,
                    CASE WHEN b.TEMP_INCENTIVE_COST_CUT IS NULL THEN '0' ELSE ROUND(b.TEMP_INCENTIVE_COST_CUT) END as TEMP_INCENTIVE_COST_CUT_ROUND,
                    (CASE 
                      WHEN b.TEMP_INCENTIVE_PERCENT_CUT IS NULL THEN 0
                      ELSE b.TEMP_INCENTIVE_PERCENT_CUT
                    END) AS TEMP_INCENTIVE_PERCENT_CUT
                    FROM (SELECT EMPLOYEE_NIK, TEMP_INCENTIVE_PERCENT_CUT, TEMP_INCENTIVE_TOTAL, TEMP_INCENTIVE_COST_CUT FROM tx_temp_incentive WHERE INCENTIVE_PARAM_ID = '".$incentiveparam."') b
                    LEFT JOIN tm_employee AS a
                    ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK AND a.EMPLOYEE_STATUS = 1
                    WHERE a.EMPLOYEE_NAME like '%$text%' OR a.EMPLOYEE_NIK like '%$text%'
                    ORDER BY a.EMPLOYEE_SLICE ASC";

        $data = DB::select($datas);
        
        return response($data);
    }

    public function getQccGroupA($id){

        $datas = "SELECT a.EMPLOYEE_NIK, a.EMPLOYEE_NAME, 
                        (CASE 
                          WHEN b.BAE_BOX_HANDLE IS NULL THEN 0
                          ELSE b.BAE_BOX_HANDLE
                        END) AS EMPLOYEE_BOX_TEMP,
                        (CASE 
                          WHEN b.BAE_OJEKER_BOX IS NULL THEN 0
                          ELSE b.BAE_OJEKER_BOX
                        END) AS EMPLOYEE_BOX_OJEK
                    FROM tm_employee a
                    LEFT JOIN (SELECT EMPLOYEE_NIK, BAE_BOX_HANDLE, BAE_OJEKER_BOX FROM tx_box_adjusment_emp WHERE INCENTIVE_PARAM_ID = '".$id."') AS b 
                        ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK
                    WHERE a.EMPLOYEE_SLICE = 1 and a.EMPLOYEE_GROUP = 8 and a.EMPLOYEE_BLOCK = 1 and a.EMPLOYEE_STATUS = 1";

        $data = DB::select($datas);

        // $data = DB::table('tm_employee')->select('tm_employee.EMPLOYEE_NIK','tm_employee.EMPLOYEE_NAME','tm_employee.EMPLOYEE_BOX_TEMP','tm_employee.EMPLOYEE_BOX_OJEK')
        //                                 ->leftjoin('tx_box_adjusment_emp')
        //                                 ->where('tm_employee.EMPLOYEE_POS',66)
        //                                 ->where('tm_employee.EMPLOYEE_GROUP',8)
        //                                 ->where('tm_employee.EMPLOYEE_BLOCK',1)
        //                                 ->get();

        return response($data);
    }

    public function getQccGroupB($id){
        $datas = "SELECT a.EMPLOYEE_NIK, a.EMPLOYEE_NAME, 
                        (CASE 
                          WHEN b.BAE_BOX_HANDLE IS NULL THEN 0
                          ELSE b.BAE_BOX_HANDLE
                        END) AS EMPLOYEE_BOX_TEMP,
                        (CASE 
                          WHEN b.BAE_OJEKER_BOX IS NULL THEN 0
                          ELSE b.BAE_OJEKER_BOX
                        END) AS EMPLOYEE_BOX_OJEK
                    FROM tm_employee a
                    LEFT JOIN (SELECT EMPLOYEE_NIK, BAE_BOX_HANDLE, BAE_OJEKER_BOX FROM tx_box_adjusment_emp WHERE INCENTIVE_PARAM_ID = '".$id."') AS b 
                        ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK
                    WHERE a.EMPLOYEE_SLICE = 1 and a.EMPLOYEE_GROUP = 9 and a.EMPLOYEE_BLOCK = 1 and a.EMPLOYEE_STATUS = 1";

        $data = DB::select($datas);

        // $data = DB::table('tm_employee')->select('tm_employee.EMPLOYEE_NIK','tm_employee.EMPLOYEE_NAME','tm_employee.EMPLOYEE_BOX_TEMP','tm_employee.EMPLOYEE_BOX_OJEK')
        //                                 ->where('tm_employee.EMPLOYEE_POS',66)
        //                                 ->where('tm_employee.EMPLOYEE_GROUP',9)
        //                                 ->where('tm_employee.EMPLOYEE_BLOCK',1)
        //                                 ->get();
        return response($data);
    }

    public function getQccGroupC($id){
        $datas = "SELECT a.EMPLOYEE_NIK, a.EMPLOYEE_NAME, 
                        (CASE 
                          WHEN b.BAE_BOX_HANDLE IS NULL THEN 0
                          ELSE b.BAE_BOX_HANDLE
                        END) AS EMPLOYEE_BOX_TEMP,
                        (CASE 
                          WHEN b.BAE_OJEKER_BOX IS NULL THEN 0
                          ELSE b.BAE_OJEKER_BOX
                        END) AS EMPLOYEE_BOX_OJEK
                    FROM tm_employee a
                    LEFT JOIN (SELECT EMPLOYEE_NIK, BAE_BOX_HANDLE, BAE_OJEKER_BOX FROM tx_box_adjusment_emp WHERE INCENTIVE_PARAM_ID = '".$id."') AS b 
                        ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK
                    WHERE a.EMPLOYEE_SLICE = 1 and a.EMPLOYEE_GROUP = 10 and a.EMPLOYEE_BLOCK = 1 and a.EMPLOYEE_STATUS = 1";

        $data = DB::select($datas);

        // $data = DB::table('tm_employee')->select('tm_employee.EMPLOYEE_NIK','tm_employee.EMPLOYEE_NAME','tm_employee.EMPLOYEE_BOX_TEMP','tm_employee.EMPLOYEE_BOX_OJEK')
        //                                 ->where('tm_employee.EMPLOYEE_POS',66)
        //                                 ->where('tm_employee.EMPLOYEE_GROUP',10)
        //                                 ->where('tm_employee.EMPLOYEE_BLOCK',1)
        //                                 ->get();
        return response($data);
    }

    public function getQccGroupD($id){
        $datas = "SELECT a.EMPLOYEE_NIK, a.EMPLOYEE_NAME, 
                        (CASE 
                          WHEN b.BAE_BOX_HANDLE IS NULL THEN 0
                          ELSE b.BAE_BOX_HANDLE
                        END) AS EMPLOYEE_BOX_TEMP,
                        (CASE 
                          WHEN b.BAE_OJEKER_BOX IS NULL THEN 0
                          ELSE b.BAE_OJEKER_BOX
                        END) AS EMPLOYEE_BOX_OJEK
                    FROM tm_employee a
                    LEFT JOIN (SELECT EMPLOYEE_NIK, BAE_BOX_HANDLE, BAE_OJEKER_BOX FROM tx_box_adjusment_emp WHERE INCENTIVE_PARAM_ID = '".$id."') AS b 
                        ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK
                    WHERE a.EMPLOYEE_SLICE = 1 and a.EMPLOYEE_GROUP = 11 and a.EMPLOYEE_BLOCK = 1 and a.EMPLOYEE_STATUS = 1";

        $data = DB::select($datas);

        // $data = DB::table('tm_employee')->select('tm_employee.EMPLOYEE_NIK','tm_employee.EMPLOYEE_NAME','tm_employee.EMPLOYEE_BOX_TEMP','tm_employee.EMPLOYEE_BOX_OJEK')
        //                                 ->where('tm_employee.EMPLOYEE_POS',66)
        //                                 ->where('tm_employee.EMPLOYEE_GROUP',11)
        //                                 ->where('tm_employee.EMPLOYEE_BLOCK',1)
        //                                 ->get();
        return response($data);
    }

    public function getRtgGroupA($id){
        $datas = "SELECT a.EMPLOYEE_NIK, a.EMPLOYEE_NAME, 
                        (CASE 
                          WHEN b.BAE_BOX_HANDLE IS NULL THEN 0
                          ELSE b.BAE_BOX_HANDLE
                        END) AS EMPLOYEE_BOX_TEMP,
                        (CASE 
                          WHEN b.BAE_OJEKER_BOX IS NULL THEN 0
                          ELSE b.BAE_OJEKER_BOX
                        END) AS EMPLOYEE_BOX_OJEK
                    FROM tm_employee a
                    LEFT JOIN (SELECT EMPLOYEE_NIK, BAE_BOX_HANDLE, BAE_OJEKER_BOX FROM tx_box_adjusment_emp WHERE INCENTIVE_PARAM_ID = '".$id."') AS b 
                        ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK
                    WHERE a.EMPLOYEE_SLICE = 2 and a.EMPLOYEE_GROUP = 8 and a.EMPLOYEE_BLOCK = 1 and a.EMPLOYEE_STATUS = 1";

        $data = DB::select($datas);

        // $data = DB::table('tm_employee')->select('tm_employee.EMPLOYEE_NIK','tm_employee.EMPLOYEE_NAME','tm_employee.EMPLOYEE_BOX_TEMP',
        //                                         'tm_employee.EMPLOYEE_BOX_OJEK')
        //                                 ->where('tm_employee.EMPLOYEE_POS',67)
        //                                 ->where('tm_employee.EMPLOYEE_GROUP',8)
        //                                 ->where('tm_employee.EMPLOYEE_BLOCK',1)
        //                                 ->get();
        return response($data);
    }

    public function getRtgGroupB($id){
        $datas = "SELECT a.EMPLOYEE_NIK, a.EMPLOYEE_NAME, 
                        (CASE 
                          WHEN b.BAE_BOX_HANDLE IS NULL THEN 0
                          ELSE b.BAE_BOX_HANDLE
                        END) AS EMPLOYEE_BOX_TEMP,
                        (CASE 
                          WHEN b.BAE_OJEKER_BOX IS NULL THEN 0
                          ELSE b.BAE_OJEKER_BOX
                        END) AS EMPLOYEE_BOX_OJEK
                    FROM tm_employee a
                    LEFT JOIN (SELECT EMPLOYEE_NIK, BAE_BOX_HANDLE, BAE_OJEKER_BOX FROM tx_box_adjusment_emp WHERE INCENTIVE_PARAM_ID = '".$id."') AS b 
                        ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK
                    WHERE a.EMPLOYEE_SLICE = 2 and a.EMPLOYEE_GROUP = 9 and a.EMPLOYEE_BLOCK = 1 and a.EMPLOYEE_STATUS = 1";

        $data = DB::select($datas);

        // $data = DB::table('tm_employee')->select('tm_employee.EMPLOYEE_NIK','tm_employee.EMPLOYEE_NAME','tm_employee.EMPLOYEE_BOX_TEMP',
        //                                         'tm_employee.EMPLOYEE_BOX_OJEK')
        //                                 ->where('tm_employee.EMPLOYEE_POS',67)
        //                                 ->where('tm_employee.EMPLOYEE_GROUP',9)
        //                                 ->where('tm_employee.EMPLOYEE_BLOCK',1)
        //                                 ->get();
        return response($data);
    }

    public function getRtgGroupC($id){
        $datas = "SELECT a.EMPLOYEE_NIK, a.EMPLOYEE_NAME, 
                        (CASE 
                          WHEN b.BAE_BOX_HANDLE IS NULL THEN 0
                          ELSE b.BAE_BOX_HANDLE
                        END) AS EMPLOYEE_BOX_TEMP,
                        (CASE 
                          WHEN b.BAE_OJEKER_BOX IS NULL THEN 0
                          ELSE b.BAE_OJEKER_BOX
                        END) AS EMPLOYEE_BOX_OJEK
                    FROM tm_employee a
                    LEFT JOIN (SELECT EMPLOYEE_NIK, BAE_BOX_HANDLE, BAE_OJEKER_BOX FROM tx_box_adjusment_emp WHERE INCENTIVE_PARAM_ID = '".$id."') AS b 
                        ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK
                    WHERE a.EMPLOYEE_SLICE = 2 and a.EMPLOYEE_GROUP = 10 and a.EMPLOYEE_BLOCK = 1 and a.EMPLOYEE_STATUS = 1";

        $data = DB::select($datas);

        // $data = DB::table('tm_employee')->select('tm_employee.EMPLOYEE_NIK','tm_employee.EMPLOYEE_NAME','tm_employee.EMPLOYEE_BOX_TEMP',
        //                                         'tm_employee.EMPLOYEE_BOX_OJEK')
        //                                 ->where('tm_employee.EMPLOYEE_POS',67)
        //                                 ->where('tm_employee.EMPLOYEE_GROUP',10)
        //                                 ->where('tm_employee.EMPLOYEE_BLOCK',1)
        //                                 ->get();
        return response($data);
    }

    public function getRtgGroupD($id){
        $datas = "SELECT a.EMPLOYEE_NIK, a.EMPLOYEE_NAME, 
                        (CASE 
                          WHEN b.BAE_BOX_HANDLE IS NULL THEN 0
                          ELSE b.BAE_BOX_HANDLE
                        END) AS EMPLOYEE_BOX_TEMP,
                        (CASE 
                          WHEN b.BAE_OJEKER_BOX IS NULL THEN 0
                          ELSE b.BAE_OJEKER_BOX
                        END) AS EMPLOYEE_BOX_OJEK
                    FROM tm_employee a
                    LEFT JOIN (SELECT EMPLOYEE_NIK, BAE_BOX_HANDLE, BAE_OJEKER_BOX FROM tx_box_adjusment_emp WHERE INCENTIVE_PARAM_ID = '".$id."') AS b 
                        ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK
                    WHERE a.EMPLOYEE_SLICE = 2 and a.EMPLOYEE_GROUP = 11 and a.EMPLOYEE_BLOCK = 1 and a.EMPLOYEE_STATUS = 1";

        $data = DB::select($datas);

        // $data = DB::table('tm_employee')->select('tm_employee.EMPLOYEE_NIK','tm_employee.EMPLOYEE_NAME','tm_employee.EMPLOYEE_BOX_TEMP', 
        //                                         'tm_employee.EMPLOYEE_BOX_OJEK')
        //                                 ->where('tm_employee.EMPLOYEE_POS',67)
        //                                 ->where('tm_employee.EMPLOYEE_GROUP',11)
        //                                 ->where('tm_employee.EMPLOYEE_BLOCK',1)
        //                                 ->get();
        return response($data);
    }

    public function saveBoxGroupA(Request $request, $id){
        $fixing = explode('&', $id);
        $incentiveparam = $fixing[0];
        $box_groupid = $fixing[1];
        $boxpergroup = $fixing[2];
        $insentifperunit = $fixing[3];
        $nik = $request->get('EMPLOYEE_NIK');
        $boxhandle = $request->get('EMPLOYEE_BOX_TEMP');
        $insentifmoney = $boxhandle / $boxpergroup * $insentifperunit;

        $groupA = BoxAdjusmentEmpModel::updateOrCreate([
                                                        'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                        'EMPLOYEE_NIK' => $nik
                                                        ],
                                                        ['BAE_BOX_HANDLE' => $boxhandle,
                                                        'BOX_ADJUSMENT_GROUP_ID' => $box_groupid,
                                                        ]
                                                     );

        $groupA->save();

        $updateParam = DB::table('tx_incentive_param')
                        ->where('INCENTIVE_PARAM_ID', $incentiveparam)
                        ->update(['INCENTIVE_PARAM_STATUS' => 3]);

        return Response::json(array('success'=>true));
    }

    public function saveBoxGroupB(Request $request, $id){
        $fixing = explode('&', $id);
        $incentiveparam = $fixing[0];
        $box_groupid = $fixing[1];
        $boxpergroup = $fixing[2];
        $insentifperunit = $fixing[3];
        $nik = $request->get('EMPLOYEE_NIK');
        $boxhandle = $request->get('EMPLOYEE_BOX_TEMP');
        $insentifmoney = $boxhandle / $boxpergroup * $insentifperunit;

        $groupA = BoxAdjusmentEmpModel::updateOrCreate([
                                                        'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                        'EMPLOYEE_NIK' => $nik
                                                        ],
                                                        ['BAE_BOX_HANDLE' => $boxhandle,
                                                        'BOX_ADJUSMENT_GROUP_ID' => $box_groupid,
                                                        ]
                                                     );
        $groupA->save();

        return Response::json(array('success'=>true));
    }

    public function saveBoxGroupC(Request $request, $id){
        $fixing = explode('&', $id);
        $incentiveparam = $fixing[0];
        $box_groupid = $fixing[1];
        $boxpergroup = $fixing[2];
        $insentifperunit = $fixing[3];
        $nik = $request->get('EMPLOYEE_NIK');
        $boxhandle = $request->get('EMPLOYEE_BOX_TEMP');
        $insentifmoney = $boxhandle / $boxpergroup * $insentifperunit;

        $groupA = BoxAdjusmentEmpModel::updateOrCreate([
                                                        'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                        'EMPLOYEE_NIK' => $nik
                                                        ],
                                                        ['BAE_BOX_HANDLE' => $boxhandle,
                                                        'BOX_ADJUSMENT_GROUP_ID' => $box_groupid,
                                                        ]
                                                     );
        $groupA->save();

        return Response::json(array('success'=>true));
    }

    public function saveBoxGroupD(Request $request, $id){
        $fixing = explode('&', $id);
        $incentiveparam = $fixing[0];
        $box_groupid = $fixing[1];
        $boxpergroup = $fixing[2];
        $insentifperunit = $fixing[3];
        $nik = $request->get('EMPLOYEE_NIK');
        $boxhandle = $request->get('EMPLOYEE_BOX_TEMP');
        $insentifmoney = $boxhandle / $boxpergroup * $insentifperunit;

        $groupA = BoxAdjusmentEmpModel::updateOrCreate([
                                                        'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                        'EMPLOYEE_NIK' => $nik
                                                        ],
                                                        ['BAE_BOX_HANDLE' => $boxhandle,
                                                        'BOX_ADJUSMENT_GROUP_ID' => $box_groupid,
                                                        ]
                                                     );
        $groupA->save();

        return Response::json(array('success'=>true));
    }

    public function saveBoxGroupRtgA(Request $request, $id){
        $fixing = explode('&', $id);
        $incentiveparam = $fixing[0];
        $box_groupid = $fixing[1];
        $boxpergroup = $fixing[2];
        $insentifperunit = $fixing[3];
        $nik = $request->get('EMPLOYEE_NIK');
        $boxhandle = $request->get('EMPLOYEE_BOX_TEMP');
        $insentifmoney = $boxhandle / $boxpergroup * $insentifperunit;

        $groupA = BoxAdjusmentEmpModel::updateOrCreate([
                                                        'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                        'EMPLOYEE_NIK' => $nik
                                                        ],
                                                        ['BAE_BOX_HANDLE' => $boxhandle,
                                                        'BOX_ADJUSMENT_GROUP_ID' => $box_groupid,
                                                        ]
                                                     );
        $groupA->save();

        return Response::json(array('success'=>true));
    }

    public function saveBoxGroupRtgB(Request $request, $id){
        $fixing = explode('&', $id);
        $incentiveparam = $fixing[0];
        $box_groupid = $fixing[1];
        $boxpergroup = $fixing[2];
        $insentifperunit = $fixing[3];
        $nik = $request->get('EMPLOYEE_NIK');
        $boxhandle = $request->get('EMPLOYEE_BOX_TEMP');
        $insentifmoney = $boxhandle / $boxpergroup * $insentifperunit;

        $groupA = BoxAdjusmentEmpModel::updateOrCreate([
                                                        'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                        'EMPLOYEE_NIK' => $nik
                                                        ],
                                                        ['BAE_BOX_HANDLE' => $boxhandle,
                                                        'BOX_ADJUSMENT_GROUP_ID' => $box_groupid,
                                                        ]
                                                     );
        $groupA->save();

        return Response::json(array('success'=>true));
    }

    public function saveBoxGroupRtgC(Request $request, $id){
        $fixing = explode('&', $id);
        $incentiveparam = $fixing[0];
        $box_groupid = $fixing[1];
        $boxpergroup = $fixing[2];
        $insentifperunit = $fixing[3];
        $nik = $request->get('EMPLOYEE_NIK');
        $boxhandle = $request->get('EMPLOYEE_BOX_TEMP');
        $insentifmoney = $boxhandle / $boxpergroup * $insentifperunit;

        $groupA = BoxAdjusmentEmpModel::updateOrCreate([
                                                        'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                        'EMPLOYEE_NIK' => $nik
                                                        ],
                                                        ['BAE_BOX_HANDLE' => $boxhandle,
                                                        'BOX_ADJUSMENT_GROUP_ID' => $box_groupid,
                                                        ]
                                                     );
        $groupA->save();


        return Response::json(array('success'=>true));
    }

    public function saveBoxGroupRtgD(Request $request, $id){        
        $fixing = explode('&', $id);
        $incentiveparam = $fixing[0];
        $box_groupid = $fixing[1];
        $boxpergroup = $fixing[2];
        $insentifperunit = $fixing[3];
        $nik = $request->get('EMPLOYEE_NIK');
        $boxhandle = $request->get('EMPLOYEE_BOX_TEMP');
        $insentifmoney = $boxhandle / $boxpergroup * $insentifperunit;

        $groupA = BoxAdjusmentEmpModel::updateOrCreate([
                                                        'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                        'EMPLOYEE_NIK' => $nik
                                                        ],
                                                        ['BAE_BOX_HANDLE' => $boxhandle,
                                                        'BOX_ADJUSMENT_GROUP_ID' => $box_groupid,
                                                        ]
                                                     );
        $groupA->save();

        return Response::json(array('success'=>true));
    }

    public function savePotonganKar(Request $request, $id){
        $incentiveparam = $id;
        $nik = $request->get('EMPLOYEE_NIK');
        $potongan = $request->get('TEMP_INCENTIVE_PERCENT_CUT');        
        
        $groupA = TempInsentifModel::updateOrCreate([
                                                        'INCENTIVE_PARAM_ID' => $id,
                                                        'EMPLOYEE_NIK' => $nik
                                                        ],
                                                        ['TEMP_INCENTIVE_PERCENT_CUT' => $potongan
                                                        ]
                                                     );
        $groupA->save();

        return Response::json(array('success'=>true));
    }

    public function saveBoxOjekQccA(Request $request, $id){
        $fixing = explode('&', $id);
        $incentiveparam = $fixing[0];
        $box_groupid = $fixing[1];
        $hargaRtgBox = $fixing[2];
        $nik = $request->get('EMPLOYEE_NIK');
        $boxojek = $request->get('EMPLOYEE_BOX_OJEK');
        $totalojekinsentif = $boxojek * $hargaRtgBox;

        $udpate = BoxAdjusmentEmpModel::updateOrCreate([
                                                        'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                        'EMPLOYEE_NIK' => $nik
                                                        ],
                                                        [
                                                        'BAE_OJEKER_BOX' => $boxojek,
                                                        'BOX_ADJUSMENT_GROUP_ID' => $box_groupid
                                                        ]
                                                     );
        $udpate->save();
        return Response::json(array('success'=>true));


    }

    public function saveBoxOjekQccB(Request $request, $id){
        $fixing = explode('&', $id);
        $incentiveparam = $fixing[0];
        $box_groupid = $fixing[1];
        $hargaRtgBox = $fixing[2];
        $nik = $request->get('EMPLOYEE_NIK');
        $boxojek = $request->get('EMPLOYEE_BOX_OJEK');
        $totalojekinsentif = $boxojek * $hargaRtgBox;

        $udpate = BoxAdjusmentEmpModel::updateOrCreate([
                                                        'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                        'EMPLOYEE_NIK' => $nik
                                                        ],
                                                        ['BAE_OJEKER_BOX' => $boxojek,
                                                        'BOX_ADJUSMENT_GROUP_ID' => $box_groupid
                                                        ]
                                                     );
        $udpate->save();
        return Response::json(array('success'=>true));


    }

    public function saveBoxOjekQccC(Request $request, $id){
        $fixing = explode('&', $id);
        $incentiveparam = $fixing[0];
        $box_groupid = $fixing[1];
        $hargaRtgBox = $fixing[2];
        $nik = $request->get('EMPLOYEE_NIK');
        $boxojek = $request->get('EMPLOYEE_BOX_OJEK');
        $totalojekinsentif = $boxojek * $hargaRtgBox;

        $udpate = BoxAdjusmentEmpModel::updateOrCreate([
                                                        'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                        'EMPLOYEE_NIK' => $nik
                                                        ],
                                                        ['BAE_OJEKER_BOX' => $boxojek,
                                                        'BOX_ADJUSMENT_GROUP_ID' => $box_groupid
                                                        ]
                                                     );
        $udpate->save();


        return Response::json(array('success'=>true));


    }

    public function saveBoxOjekQccD(Request $request, $id){
        $fixing = explode('&', $id);
        $incentiveparam = $fixing[0];
        $box_groupid = $fixing[1];
        $hargaRtgBox = $fixing[2];
        $nik = $request->get('EMPLOYEE_NIK');
        $boxojek = $request->get('EMPLOYEE_BOX_OJEK');
        $totalojekinsentif = $boxojek * $hargaRtgBox;

        $udpate = BoxAdjusmentEmpModel::updateOrCreate([
                                                        'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                        'EMPLOYEE_NIK' => $nik
                                                        ],
                                                        ['BAE_OJEKER_BOX' => $boxojek,
                                                        'BOX_ADJUSMENT_GROUP_ID' => $box_groupid
                                                        ]
                                                     );
        $udpate->save();

        return Response::json(array('success'=>true));


    }

    public function saveBoxOjekRtgA(Request $request, $id){
        $fixing = explode('&', $id);
        $incentiveparam = $fixing[0];
        $box_groupid = $fixing[1];
        $hargaRtgBox = $fixing[2];
        $nik = $request->get('EMPLOYEE_NIK');
        $boxojek = $request->get('EMPLOYEE_BOX_OJEK');
        $totalojekinsentif = $boxojek * $hargaRtgBox;

        $udpate = BoxAdjusmentEmpModel::updateOrCreate([
                                                        'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                        'EMPLOYEE_NIK' => $nik
                                                        ],
                                                        ['BAE_OJEKER_BOX' => $boxojek,
                                                        'BOX_ADJUSMENT_GROUP_ID' => $box_groupid
                                                        ]
                                                     );
        $udpate->save();
        return Response::json(array('success'=>true));


    }

    public function saveBoxOjekRtgB(Request $request, $id){
        $fixing = explode('&', $id);
        $incentiveparam = $fixing[0];
        $box_groupid = $fixing[1];
        $hargaRtgBox = $fixing[2];
        $nik = $request->get('EMPLOYEE_NIK');
        $boxojek = $request->get('EMPLOYEE_BOX_OJEK');
        $totalojekinsentif = $boxojek * $hargaRtgBox;

        $udpate = BoxAdjusmentEmpModel::updateOrCreate([
                                                        'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                        'EMPLOYEE_NIK' => $nik
                                                        ],
                                                        ['BAE_OJEKER_BOX' => $boxojek,
                                                        'BOX_ADJUSMENT_GROUP_ID' => $box_groupid
                                                        ]
                                                     );
        $udpate->save();

        return Response::json(array('success'=>true));


    }

    public function saveBoxOjekRtgC(Request $request, $id){
        $fixing = explode('&', $id);
        $incentiveparam = $fixing[0];
        $box_groupid = $fixing[1];
        $hargaRtgBox = $fixing[2];
        $nik = $request->get('EMPLOYEE_NIK');
        $boxojek = $request->get('EMPLOYEE_BOX_OJEK');
        $totalojekinsentif = $boxojek * $hargaRtgBox;

        $udpate = BoxAdjusmentEmpModel::updateOrCreate([
                                                        'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                        'EMPLOYEE_NIK' => $nik
                                                        ],
                                                        ['BAE_OJEKER_BOX' => $boxojek,
                                                        'BOX_ADJUSMENT_GROUP_ID' => $box_groupid
                                                        ]
                                                     );
        $udpate->save();
        return Response::json(array('success'=>true));


    }

    public function saveBoxOjekRtgD(Request $request, $id){
        $fixing = explode('&', $id);
        $incentiveparam = $fixing[0];
        $box_groupid = $fixing[1];
        $hargaRtgBox = $fixing[2];
        $nik = $request->get('EMPLOYEE_NIK');
        $boxojek = $request->get('EMPLOYEE_BOX_OJEK');
        $totalojekinsentif = $boxojek * $hargaRtgBox;

        $udpate = BoxAdjusmentEmpModel::updateOrCreate([
                                                        'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                        'EMPLOYEE_NIK' => $nik
                                                        ],
                                                        ['BAE_OJEKER_BOX' => $boxojek,
                                                        'BOX_ADJUSMENT_GROUP_ID' => $box_groupid
                                                        ]
                                                     );
        $udpate->save();
        return Response::json(array('success'=>true));


    }

    public function previewInsentif($incentiveparam){

        $bulantahun = IncentiveParamModel::select('INCENTIVE_PARAM_YEAR',
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
                                                           END as INCENTIVE_PARAM_MONTH')
                                                  )                                                
                                        ->where('INCENTIVE_PARAM_ID',$incentiveparam)
                                        ->first();

        $bulan = $bulantahun->INCENTIVE_PARAM_MONTH;
        $tahun = $bulantahun->INCENTIVE_PARAM_YEAR;
        
        $data = EmployeeModel::select('tm_employee.EMPLOYEE_NIK','tm_employee.EMPLOYEE_NAME','tm_position.POSITION_NAME','tm_group.GROUP_NAME','tr_shift.SHIFT_NAME',
                                      'tr_block.BLOCK_NAME','tr_slice.SLICE_NAME','tx_box_adjusment_emp.BAE_BOX_HANDLE','tx_temp_incentive.TEMP_INCENTIVE_PERCENT_CUT',
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_FLAT IS NULL THEN "0" ELSE CONCAT("Rp. ", FORMAT(ROUND(tx_temp_incentive.TEMP_INCENTIVE_FLAT),0,"de_DE")) END as TEMP_INCENTIVE_FLAT'),
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_TOTAL IS NULL THEN "0" ELSE CONCAT("Rp. ", FORMAT(ROUND(tx_temp_incentive.TEMP_INCENTIVE_TOTAL),0,"de_DE")) END as TEMP_INCENTIVE_TOTAL'),
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_OJEKER IS NULL THEN "0" ELSE CONCAT("Rp. ", FORMAT(ROUND(tx_temp_incentive.TEMP_INCENTIVE_OJEKER),0,"de_DE")) END as TEMP_INCENTIVE_OJEKER'),
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_BOX IS NULL THEN "0" ELSE CONCAT("Rp. ", FORMAT(ROUND(tx_temp_incentive.TEMP_INCENTIVE_BOX),0,"de_DE")) END as TEMP_INCENTIVE_BOX'),
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_THRESHOLD IS NULL THEN "0" ELSE CONCAT("Rp. ", FORMAT(ROUND(tx_temp_incentive.TEMP_INCENTIVE_THRESHOLD),0,"de_DE")) END as TEMP_INCENTIVE_THRESHOLD'),
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_COST_CUT IS NULL THEN "0" ELSE CONCAT("Rp. ", FORMAT(ROUND(tx_temp_incentive.TEMP_INCENTIVE_COST_CUT),0,"de_DE")) END as TEMP_INCENTIVE_COST_CUT'),
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_DIST_CUT IS NULL THEN "0" ELSE CONCAT("Rp. ", FORMAT(ROUND(tx_temp_incentive.TEMP_INCENTIVE_DIST_CUT),0,"de_DE")) END as TEMP_INCENTIVE_DIST_CUT'),
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_TOTAL_AFTER_CUT IS NULL THEN "0" ELSE CONCAT("Rp. ", FORMAT(ROUND(tx_temp_incentive.TEMP_INCENTIVE_TOTAL_AFTER_CUT),0,"de_DE")) END as TEMP_INCENTIVE_TOTAL_AFTER_CUT')
                                      )
                            ->leftjoin('tx_temp_incentive',function($join) use ($incentiveparam){
                                $join->on('tm_employee.EMPLOYEE_NIK','=','tx_temp_incentive.EMPLOYEE_NIK')
                                     ->where('tx_temp_incentive.INCENTIVE_PARAM_ID','=',$incentiveparam);
                            })
                            ->leftjoin('tm_position','tm_position.POSITION_ID','=','tm_employee.EMPLOYEE_POS')
                            ->leftjoin('tm_group','tm_group.GROUP_ID','=','tm_employee.EMPLOYEE_GROUP')
                            ->leftjoin('tr_shift','tr_shift.SHIFT_ID','=','tm_employee.EMPLOYEE_SHIFT')
                            ->leftjoin('tr_block','tr_block.BLOCK_ID','=','tm_employee.EMPLOYEE_BLOCK')
                            ->leftjoin('tr_slice','tr_slice.SLICE_ID','=','tm_employee.EMPLOYEE_SLICE')
                            ->leftjoin('tx_box_adjusment_emp',function($join) use ($incentiveparam){
                                $join->on('tm_employee.EMPLOYEE_NIK','=','tx_box_adjusment_emp.EMPLOYEE_NIK')
                                     ->where('tx_box_adjusment_emp.INCENTIVE_PARAM_ID','=',$incentiveparam);
                            })
                            ->where('tm_employee.EMPLOYEE_STATUS',1)
                            ->orderby('tm_employee.EMPLOYEE_BLOCK','ASC')
                            ->orderby('tm_employee.EMPLOYEE_SLICE','ASC')
                            ->get();

        $showPortionOfSlice = PortionOfSliceModel::select(
                                                           'BLOCK_ID','SLICE_ID','POS_DIFF_PER_SLICE','POS_HEADACOUNT_PER_SLICE','POS_COEFFICIENT_UPPER_VALUE',
                                                           'POS_BOBOT','POS_PERSENTASE_SLICE','POS_PERSENTASE_SLICE_PER_EMP',
                                                           DB::raw('CONCAT("Rp. ", FORMAT(POS_KUE_PER_SLICE,0,"de_DE")) as POS_KUE_PER_SLICE'),
                                                           DB::raw('CONCAT("Rp. ", FORMAT(POS_NOM_FLAT_DIST_BLOCK_I,0,"de_DE")) as POS_NOM_FLAT_DIST_BLOCK_I')
                                                         )
                                                ->where('INCENTIVE_PARAM_ID',$incentiveparam)
                                                ->get();

        $showPortionOfBlock = PortionOfBlockModel::select('*',DB::raw('ROUND(POB_BLOCK_COMPOSITION,2) as Block_Composition'))
                                                    ->where('INCENTIVE_PARAM_ID',$incentiveparam)
                                                    ->get();

        // return $data;

        return Response::json(array('loop' => $data, 'tahun' => $tahun, 'bulan' => $bulan, 'showPortionOfSlice' => $showPortionOfSlice, 'showPortionOfBlock' => $showPortionOfBlock));
    }

    public function previewInsentifsearch($id){
        $coba = explode('&', $id);
        $incentiveparam = $coba[0];
        $text = $coba[1];

        $bulantahun = IncentiveParamModel::select('INCENTIVE_PARAM_YEAR',
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
                                                           END as INCENTIVE_PARAM_MONTH')
                                                  )                                                
                                        ->where('INCENTIVE_PARAM_ID',$incentiveparam)
                                        ->first();

        $bulan = $bulantahun->INCENTIVE_PARAM_MONTH;
        $tahun = $bulantahun->INCENTIVE_PARAM_YEAR;
        
        $data = EmployeeModel::select('tm_employee.EMPLOYEE_NIK','tm_employee.EMPLOYEE_NAME','tm_position.POSITION_NAME','tm_group.GROUP_NAME','tr_shift.SHIFT_NAME',
                                      'tr_block.BLOCK_NAME','tr_slice.SLICE_NAME','tx_box_adjusment_emp.BAE_BOX_HANDLE','tx_temp_incentive.TEMP_INCENTIVE_PERCENT_CUT',
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_FLAT IS NULL THEN "0" ELSE CONCAT("Rp. ", FORMAT(ROUND(tx_temp_incentive.TEMP_INCENTIVE_FLAT),0,"de_DE")) END as TEMP_INCENTIVE_FLAT'),
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_TOTAL IS NULL THEN "0" ELSE CONCAT("Rp. ", FORMAT(ROUND(tx_temp_incentive.TEMP_INCENTIVE_TOTAL),0,"de_DE")) END as TEMP_INCENTIVE_TOTAL'),
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_OJEKER IS NULL THEN "0" ELSE CONCAT("Rp. ", FORMAT(ROUND(tx_temp_incentive.TEMP_INCENTIVE_OJEKER),0,"de_DE")) END as TEMP_INCENTIVE_OJEKER'),
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_BOX IS NULL THEN "0" ELSE CONCAT("Rp. ", FORMAT(ROUND(tx_temp_incentive.TEMP_INCENTIVE_BOX),0,"de_DE")) END as TEMP_INCENTIVE_BOX'),
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_THRESHOLD IS NULL THEN "0" ELSE CONCAT("Rp. ", FORMAT(ROUND(tx_temp_incentive.TEMP_INCENTIVE_THRESHOLD),0,"de_DE")) END as TEMP_INCENTIVE_THRESHOLD'),
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_COST_CUT IS NULL THEN "0" ELSE CONCAT("Rp. ", FORMAT(ROUND(tx_temp_incentive.TEMP_INCENTIVE_COST_CUT),0,"de_DE")) END as TEMP_INCENTIVE_COST_CUT'),
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_DIST_CUT IS NULL THEN "0" ELSE CONCAT("Rp. ", FORMAT(ROUND(tx_temp_incentive.TEMP_INCENTIVE_DIST_CUT),0,"de_DE")) END as TEMP_INCENTIVE_DIST_CUT'),
                                      DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_TOTAL_AFTER_CUT IS NULL THEN "0" ELSE CONCAT("Rp. ", FORMAT(ROUND(tx_temp_incentive.TEMP_INCENTIVE_TOTAL_AFTER_CUT),0,"de_DE")) END as TEMP_INCENTIVE_TOTAL_AFTER_CUT')
                                      )
                            ->leftjoin('tx_temp_incentive',function($join) use ($incentiveparam){
                                $join->on('tm_employee.EMPLOYEE_NIK','=','tx_temp_incentive.EMPLOYEE_NIK')
                                     ->where('tx_temp_incentive.INCENTIVE_PARAM_ID','=',$incentiveparam);
                            })
                            ->leftjoin('tm_position','tm_position.POSITION_ID','=','tm_employee.EMPLOYEE_POS')
                            ->leftjoin('tm_group','tm_group.GROUP_ID','=','tm_employee.EMPLOYEE_GROUP')
                            ->leftjoin('tr_shift','tr_shift.SHIFT_ID','=','tm_employee.EMPLOYEE_SHIFT')
                            ->leftjoin('tr_block','tr_block.BLOCK_ID','=','tm_employee.EMPLOYEE_BLOCK')
                            ->leftjoin('tr_slice','tr_slice.SLICE_ID','=','tm_employee.EMPLOYEE_SLICE')
                            ->leftjoin('tx_box_adjusment_emp',function($join) use ($incentiveparam){
                                $join->on('tm_employee.EMPLOYEE_NIK','=','tx_box_adjusment_emp.EMPLOYEE_NIK')
                                     ->where('tx_box_adjusment_emp.INCENTIVE_PARAM_ID','=',$incentiveparam);
                            })
                            ->where('tm_employee.EMPLOYEE_STATUS',1)
                            ->where('tm_employee.EMPLOYEE_NAME','like','%' . $text . '%')
                            ->orWhere('tm_employee.EMPLOYEE_NIK','like','%' . $text . '%')
                            ->orWhere('tr_block.BLOCK_NAME','like','%' . $text . '%')
                            ->orWhere('tr_slice.SLICE_NAME','like','%' . $text . '%')
                            ->orWhere('tm_position.POSITION_NAME','like','%' . $text . '%')
                            ->orderby('tm_employee.EMPLOYEE_BLOCK','ASC')
                            ->orderby('tm_employee.EMPLOYEE_SLICE','ASC')
                            ->get();

        $showPortionOfSlice = PortionOfSliceModel::select(
                                                           'BLOCK_ID','SLICE_ID','POS_DIFF_PER_SLICE','POS_HEADACOUNT_PER_SLICE','POS_COEFFICIENT_UPPER_VALUE',
                                                           'POS_BOBOT','POS_PERSENTASE_SLICE','POS_PERSENTASE_SLICE_PER_EMP',
                                                           DB::raw('CONCAT("Rp. ", FORMAT(POS_KUE_PER_SLICE,0,"de_DE")) as POS_KUE_PER_SLICE'),
                                                           DB::raw('CONCAT("Rp. ", FORMAT(POS_NOM_FLAT_DIST_BLOCK_I,0,"de_DE")) as POS_NOM_FLAT_DIST_BLOCK_I')
                                                         )
                                                ->where('INCENTIVE_PARAM_ID',$incentiveparam)
                                                ->get();

        $showPortionOfBlock = PortionOfBlockModel::select('*', DB::raw('ROUND(POB_BLOCK_COMPOSITION,2) as Block_Composition'))
                                                    ->where('INCENTIVE_PARAM_ID',$incentiveparam)
                                                    ->get();

        // return $data;

        return Response::json(array('loop' => $data, 'tahun' => $tahun, 'bulan' => $bulan, 'showPortionOfSlice' => $showPortionOfSlice, 'showPortionOfBlock' => $showPortionOfBlock));
    }

    public function exportexcel($parameter="")
    {        
        if ($parameter != "") {
            $bulantahun = explode('-', $parameter);
            $bulan = $bulantahun[0];
            $tahun = $bulantahun[1];

            $incentiveparamlast = IncentiveParamModel::select('INCENTIVE_PARAM_ID')->where('INCENTIVE_PARAM_MONTH',$bulan)->where('INCENTIVE_PARAM_YEAR',$tahun)->orderby('INCENTIVE_PARAM_ID','DESC')->get();
            $incentiveparam = $incentiveparamlast[0]->INCENTIVE_PARAM_ID;    
        }else{            
            $incentiveparamlast = IncentiveParamModel::select('INCENTIVE_PARAM_ID')->where('INCENTIVE_PARAM_STATUS','5')->orderby('INCENTIVE_PARAM_ID','DESC')->get();
            $incentiveparam = $incentiveparamlast[0]->INCENTIVE_PARAM_ID;
        }


        $bulantahun = IncentiveParamModel::select('INCENTIVE_PARAM_YEAR',
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
                                                           END as INCENTIVE_PARAM_MONTH')
                                                  )                                                
                                        ->where('INCENTIVE_PARAM_ID',$incentiveparam)
                                        ->first();

        $bulan = $bulantahun->INCENTIVE_PARAM_MONTH;
        $tahun = $bulantahun->INCENTIVE_PARAM_YEAR;

        if ($parameter != "") {
            $data = PostingIncentiveModel::select(
                                                    'tx_posting_incentive.POSTING_INCENTIVE_NIK as EMPLOYEE_NIK',
                                                    'tx_posting_incentive.POSTING_INCENTIVE_NAME as EMPLOYEE_NAME',
                                                    'tm_position.POSITION_NAME','tm_group.GROUP_NAME','tr_shift.SHIFT_NAME','tr_block.BLOCK_NAME',
                                                    'tr_slice.SLICE_NAME','tx_box_adjusment_emp.BAE_BOX_HANDLE',
                                                    'tx_posting_incentive.POSTING_INCENTIVE_PERCENT_CUT as INCENTIVE_PERCENT_CUT',
                                                    DB::raw('POSTING_INCENTIVE_FLAT as INCENTIVE_FLAT'),
                                                    DB::raw('POSTING_INCENTIVE_FINAL as INCENTIVE_TOTAL'),
                                                    DB::raw('POSTING_INCENTIVE_OJEKER as INCENTIVE_OJEKER'),
                                                    DB::raw('POSTING_INCENTIVE_BOX as INCENTIVE_BOX'),
                                                    DB::raw('POSTING_INCENTIVE_THRESHOLD as INCENTIVE_THRESHOLD'),
                                                    DB::raw('POSTING_INCENTIVE_COST_CUT as INCENTIVE_COST_CUT'),
                                                    DB::raw('POSTING_INCENTIVE_DIST_CUT as INCENTIVE_DIST_CUT'),
                                                    DB::raw('POSTING_INCENTIVE_FINAL_AFTER_CUT as INCENTIVE_TOTAL_AFTER_CUT')
                                                    )
                                            ->leftjoin('tx_box_adjusment_emp',function($join) use ($incentiveparam){
                                                $join->on('tx_posting_incentive.POSTING_INCENTIVE_NIK','=','tx_box_adjusment_emp.EMPLOYEE_NIK')
                                                     ->where('tx_box_adjusment_emp.INCENTIVE_PARAM_ID','=',$incentiveparam);
                                            })
                                            ->leftjoin('tm_position','tm_position.POSITION_ID','=','tx_posting_incentive.POSTING_INCENTIVE_POS')
                                            ->leftjoin('tm_group','tm_group.GROUP_ID','=','tx_posting_incentive.POSTING_INCENTIVE_GROUP')
                                            ->leftjoin('tr_shift','tr_shift.SHIFT_ID','=','tx_posting_incentive.POSTING_INCENTIVE_SHIFT')
                                            ->leftjoin('tr_block','tr_block.BLOCK_ID','=','tx_posting_incentive.POSTING_INCENTIVE_BLOCK')
                                            ->leftjoin('tm_division','tm_division.DIVISION_ID','=','tx_posting_incentive.POSTING_INCENTIVE_DIV')
                                            ->leftjoin('tr_slice','tr_slice.SLICE_ID','=','POSTING_INCENTIVE_SLICE')
                                            ->where('tx_posting_incentive.INCENTIVE_PARAM_ID',$incentiveparam)
                                            ->orderby('tx_posting_incentive.POSTING_INCENTIVE_NAME','ASC')
                                            ->orderby('tx_posting_incentive.POSTING_INCENTIVE_BLOCK','ASC')
                                            ->get();

            $sum_incentive = PostingIncentiveModel::select(
                                          DB::raw('CASE WHEN SUM(tx_posting_incentive.POSTING_INCENTIVE_FLAT) IS NULL THEN 0 ELSE SUM(tx_posting_incentive.POSTING_INCENTIVE_FLAT) END as SUM_INCENTIVE_FLAT'),
                                          DB::raw('CASE WHEN SUM(tx_posting_incentive.POSTING_INCENTIVE_BOX) IS NULL THEN 0 ELSE SUM(tx_posting_incentive.POSTING_INCENTIVE_BOX) END as SUM_INCENTIVE_BOX'),
                                          DB::raw('CASE WHEN SUM(tx_posting_incentive.POSTING_INCENTIVE_OJEKER) IS NULL THEN 0 ELSE SUM(tx_posting_incentive.POSTING_INCENTIVE_OJEKER) END as SUM_INCENTIVE_OJEKER'),
                                          DB::raw('CASE WHEN SUM(tx_posting_incentive.POSTING_INCENTIVE_THRESHOLD) IS NULL THEN 0 ELSE SUM(tx_posting_incentive.POSTING_INCENTIVE_THRESHOLD) END as SUM_INCENTIVE_THRESHOLD'),
                                          DB::raw('CASE WHEN SUM(tx_posting_incentive.POSTING_INCENTIVE_FINAL) IS NULL THEN 0 ELSE SUM(tx_posting_incentive.POSTING_INCENTIVE_FINAL) END as SUM_INCENTIVE_TOTAL'),
                                          DB::raw('CASE WHEN SUM(tx_posting_incentive.POSTING_INCENTIVE_COST_CUT) IS NULL THEN 0 ELSE SUM(tx_posting_incentive.POSTING_INCENTIVE_COST_CUT) END as SUM_INCENTIVE_COST_CUT'),
                                          DB::raw('CASE WHEN SUM(tx_posting_incentive.POSTING_INCENTIVE_DIST_CUT) IS NULL THEN 0 ELSE SUM(tx_posting_incentive.POSTING_INCENTIVE_DIST_CUT) END as SUM_INCENTIVE_DIST_CUT'),
                                          DB::raw('CASE WHEN SUM(tx_posting_incentive.POSTING_INCENTIVE_FINAL_AFTER_CUT) IS NULL THEN 0 ELSE SUM(tx_posting_incentive.POSTING_INCENTIVE_FINAL_AFTER_CUT) END as SUM_INCENTIVE_TOTAL_AFTER_CUT')
                                      )
                                    ->where('INCENTIVE_PARAM_ID',$incentiveparam)
                                    ->get();


        }else{        
            $data = EmployeeModel::select('tm_employee.EMPLOYEE_NIK','tm_employee.EMPLOYEE_NAME','tm_position.POSITION_NAME','tm_group.GROUP_NAME','tr_shift.SHIFT_NAME',
                                          'tr_block.BLOCK_NAME','tr_slice.SLICE_NAME','tx_box_adjusment_emp.BAE_BOX_HANDLE',
                                          'tx_temp_incentive.TEMP_INCENTIVE_PERCENT_CUT as INCENTIVE_PERCENT_CUT',                                         
                                          DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_FLAT IS NULL THEN 0 ELSE tx_temp_incentive.TEMP_INCENTIVE_FLAT END as INCENTIVE_FLAT'),
                                          DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_TOTAL IS NULL THEN 0 ELSE tx_temp_incentive.TEMP_INCENTIVE_TOTAL END as INCENTIVE_TOTAL'),
                                          DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_OJEKER IS NULL THEN 0 ELSE tx_temp_incentive.TEMP_INCENTIVE_OJEKER END as INCENTIVE_OJEKER'),
                                          DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_BOX IS NULL THEN 0 ELSE tx_temp_incentive.TEMP_INCENTIVE_BOX END as INCENTIVE_BOX'),
                                          DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_THRESHOLD IS NULL THEN 0 ELSE tx_temp_incentive.TEMP_INCENTIVE_THRESHOLD END as INCENTIVE_THRESHOLD'),
                                          DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_COST_CUT IS NULL THEN 0 ELSE tx_temp_incentive.TEMP_INCENTIVE_COST_CUT END as INCENTIVE_COST_CUT'),
                                          DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_DIST_CUT IS NULL THEN 0 ELSE tx_temp_incentive.TEMP_INCENTIVE_DIST_CUT END as INCENTIVE_DIST_CUT'),
                                          DB::raw('CASE WHEN tx_temp_incentive.TEMP_INCENTIVE_TOTAL_AFTER_CUT IS NULL THEN 0 ELSE tx_temp_incentive.TEMP_INCENTIVE_TOTAL_AFTER_CUT END as INCENTIVE_TOTAL_AFTER_CUT')
                                          
                                          )
                                ->leftjoin('tx_temp_incentive',function($join) use ($incentiveparam){
                                    $join->on('tm_employee.EMPLOYEE_NIK','=','tx_temp_incentive.EMPLOYEE_NIK')
                                         ->where('tx_temp_incentive.INCENTIVE_PARAM_ID','=',$incentiveparam);
                                })
                                ->leftjoin('tm_position','tm_position.POSITION_ID','=','tm_employee.EMPLOYEE_POS')
                                ->leftjoin('tm_group','tm_group.GROUP_ID','=','tm_employee.EMPLOYEE_GROUP')
                                ->leftjoin('tr_shift','tr_shift.SHIFT_ID','=','tm_employee.EMPLOYEE_SHIFT')
                                ->leftjoin('tr_block','tr_block.BLOCK_ID','=','tm_employee.EMPLOYEE_BLOCK')
                                ->leftjoin('tr_slice','tr_slice.SLICE_ID','=','tm_employee.EMPLOYEE_SLICE')
                                ->leftjoin('tx_box_adjusment_emp',function($join) use ($incentiveparam){
                                    $join->on('tm_employee.EMPLOYEE_NIK','=','tx_box_adjusment_emp.EMPLOYEE_NIK')
                                         ->where('tx_box_adjusment_emp.INCENTIVE_PARAM_ID','=',$incentiveparam);
                                })
                                ->where('tm_employee.EMPLOYEE_STATUS',1)
                                ->orderby('tm_employee.EMPLOYEE_NAME','ASC')
                                ->orderby('tm_employee.EMPLOYEE_BLOCK','ASC')
                                // ->orderby('tm_employee.EMPLOYEE_SLICE','ASC')
                                ->get();

            $sum_incentive = TempInsentifModel::select(
                                          // DB::raw('CASE WHEN SUM(tx_temp_incentive.TEMP_INCENTIVE_FLAT) IS NULL THEN 0 ELSE FORMAT(ROUND(SUM(tx_temp_incentive.TEMP_INCENTIVE_FLAT)),0) END as SUM_INCENTIVE_FLAT'),
                                          // DB::raw('CASE WHEN SUM(tx_temp_incentive.TEMP_INCENTIVE_BOX) IS NULL THEN 0 ELSE FORMAT(ROUND(SUM(tx_temp_incentive.TEMP_INCENTIVE_BOX)),0) END as SUM_INCENTIVE_BOX'),
                                          // DB::raw('CASE WHEN SUM(tx_temp_incentive.TEMP_INCENTIVE_OJEKER) IS NULL THEN 0 ELSE FORMAT(ROUND(SUM(tx_temp_incentive.TEMP_INCENTIVE_OJEKER)),0) END as SUM_INCENTIVE_OJEKER'),
                                          // DB::raw('CASE WHEN SUM(tx_temp_incentive.TEMP_INCENTIVE_THRESHOLD) IS NULL THEN 0 ELSE FORMAT(ROUND(SUM(tx_temp_incentive.TEMP_INCENTIVE_THRESHOLD)),0) END as SUM_INCENTIVE_THRESHOLD'),
                                          // DB::raw('CASE WHEN SUM(tx_temp_incentive.TEMP_INCENTIVE_TOTAL) IS NULL THEN 0 ELSE FORMAT(ROUND(SUM(tx_temp_incentive.TEMP_INCENTIVE_TOTAL)),0) END as SUM_INCENTIVE_TOTAL'),
                                          // DB::raw('CASE WHEN SUM(tx_temp_incentive.TEMP_INCENTIVE_COST_CUT) IS NULL THEN 0 ELSE FORMAT(ROUND(SUM(tx_temp_incentive.TEMP_INCENTIVE_COST_CUT)),0) END as SUM_INCENTIVE_COST_CUT'),
                                          // DB::raw('CASE WHEN SUM(tx_temp_incentive.TEMP_INCENTIVE_DIST_CUT) IS NULL THEN 0 ELSE FORMAT(ROUND(SUM(tx_temp_incentive.TEMP_INCENTIVE_DIST_CUT)),0) END as SUM_INCENTIVE_DIST_CUT'),
                                          // DB::raw('CASE WHEN SUM(tx_temp_incentive.TEMP_INCENTIVE_TOTAL_AFTER_CUT) IS NULL THEN 0 ELSE FORMAT(ROUND(SUM(tx_temp_incentive.TEMP_INCENTIVE_TOTAL_AFTER_CUT)),0) END as SUM_INCENTIVE_TOTAL_AFTER_CUT')

                                          DB::raw('CASE WHEN SUM(tx_temp_incentive.TEMP_INCENTIVE_FLAT) IS NULL THEN 0 ELSE SUM(tx_temp_incentive.TEMP_INCENTIVE_FLAT) END as SUM_INCENTIVE_FLAT'),
                                          DB::raw('CASE WHEN SUM(tx_temp_incentive.TEMP_INCENTIVE_BOX) IS NULL THEN 0 ELSE SUM(tx_temp_incentive.TEMP_INCENTIVE_BOX) END as SUM_INCENTIVE_BOX'),
                                          DB::raw('CASE WHEN SUM(tx_temp_incentive.TEMP_INCENTIVE_OJEKER) IS NULL THEN 0 ELSE SUM(tx_temp_incentive.TEMP_INCENTIVE_OJEKER) END as SUM_INCENTIVE_OJEKER'),
                                          DB::raw('CASE WHEN SUM(tx_temp_incentive.TEMP_INCENTIVE_THRESHOLD) IS NULL THEN 0 ELSE SUM(tx_temp_incentive.TEMP_INCENTIVE_THRESHOLD) END as SUM_INCENTIVE_THRESHOLD'),
                                          DB::raw('CASE WHEN SUM(tx_temp_incentive.TEMP_INCENTIVE_TOTAL) IS NULL THEN 0 ELSE SUM(tx_temp_incentive.TEMP_INCENTIVE_TOTAL) END as SUM_INCENTIVE_TOTAL'),
                                          DB::raw('CASE WHEN SUM(tx_temp_incentive.TEMP_INCENTIVE_COST_CUT) IS NULL THEN 0 ELSE SUM(tx_temp_incentive.TEMP_INCENTIVE_COST_CUT) END as SUM_INCENTIVE_COST_CUT'),
                                          DB::raw('CASE WHEN SUM(tx_temp_incentive.TEMP_INCENTIVE_DIST_CUT) IS NULL THEN 0 ELSE SUM(tx_temp_incentive.TEMP_INCENTIVE_DIST_CUT) END as SUM_INCENTIVE_DIST_CUT'),
                                          DB::raw('CASE WHEN SUM(tx_temp_incentive.TEMP_INCENTIVE_TOTAL_AFTER_CUT) IS NULL THEN 0 ELSE SUM(tx_temp_incentive.TEMP_INCENTIVE_TOTAL_AFTER_CUT) END as SUM_INCENTIVE_TOTAL_AFTER_CUT')
                                      )
                                    ->where('INCENTIVE_PARAM_ID',$incentiveparam)
                                    ->get();
        }
        

        $showPortionOfSlice = PortionOfSliceModel::select(
                                                           'BLOCK_ID','SLICE_ID','POS_DIFF_PER_SLICE','POS_HEADACOUNT_PER_SLICE','POS_COEFFICIENT_UPPER_VALUE',
                                                           'POS_BOBOT','POS_PERSENTASE_SLICE','POS_PERSENTASE_SLICE_PER_EMP',
                                                           DB::raw('POS_KUE_PER_SLICE as POS_KUE_PER_SLICE'),
                                                           DB::raw('POS_NOM_FLAT_DIST_BLOCK_I as POS_NOM_FLAT_DIST_BLOCK_I')
                                                         )
                                                ->where('INCENTIVE_PARAM_ID',$incentiveparam)
                                                ->get();
        $sum_PortionOfSlice = PortionOfSliceModel::select(                                                           
                                                           DB::raw('SUM(POS_KUE_PER_SLICE) as SUM_POS_KUE_PER_SLICE'),
                                                           DB::raw('SUM(POS_NOM_FLAT_DIST_BLOCK_I) as SUM_POS_NOM_FLAT_DIST_BLOCK_I')
                                                         )
                                                ->where('INCENTIVE_PARAM_ID',$incentiveparam)
                                                ->get();

        $showPortionOfBlock = PortionOfBlockModel::select('*',DB::raw('ROUND(POB_BLOCK_COMPOSITION,2) as Block_Composition'))
                                                    ->where('INCENTIVE_PARAM_ID',$incentiveparam)
                                                    ->get();        

        Excel::create('Preview Insentif '.$bulan.' '.$tahun, function($excel) use($data, $sum_incentive, $tahun, $bulan, $showPortionOfSlice, $sum_PortionOfSlice, $showPortionOfBlock) {
            $excel->sheet('Insentif Karyawan', function($sheet) use($data, $sum_incentive, $tahun, $bulan) {
              $sheet->loadView('excel/insentif_karyawan')
                ->with("sum_incentive",$sum_incentive)
                ->with("data",$data)
                ->with("tahun",$tahun)
                ->with("bulan",$bulan);
            });

            $excel->sheet('Portion Of Slice', function($sheet) use($showPortionOfSlice, $sum_PortionOfSlice, $tahun, $bulan) {
              $sheet->loadView('excel/portion_of_slice')
                ->with("sum_PortionOfSlice",$sum_PortionOfSlice)
                ->with("showPortionOfSlice",$showPortionOfSlice)
                ->with("tahun",$tahun)
                ->with("bulan",$bulan);
            });

            $excel->sheet('Portion Of Block', function($sheet) use($showPortionOfBlock, $tahun, $bulan) {
              $sheet->loadView('excel/portion_of_block')
                ->with("showPortionOfBlock",$showPortionOfBlock)
                ->with("tahun",$tahun)
                ->with("bulan",$bulan);
            });
        })->export('xls');
    }

    public function upload_potongan(Request $request)
    {       
        $tgl = date("Ymd");
        $jam = date("His");

        $incentiveparam = Input::get('incentiveparam');

        $bulantahun = IncentiveParamModel::select('INCENTIVE_PARAM_YEAR',
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
                                                           END as INCENTIVE_PARAM_MONTH')
                                                  )                                                
                                        ->where('INCENTIVE_PARAM_ID',$incentiveparam)
                                        ->first();

        $bulan = $bulantahun->INCENTIVE_PARAM_MONTH;
        $tahun = $bulantahun->INCENTIVE_PARAM_YEAR;

        $destinationPath = storage_path() . '/app/excel_potongan/';
        $file = Input::file('file');        
        $doc_name = $file->getClientOriginalName();
        $doc_extension = $file->getClientOriginalExtension();
        $doc_size =$file->getClientSize();

        $extension = File::extension($file->getClientOriginalName());

        if ($extension == "xlsx" || $extension == "xls" || $extension == "csv") {
 
            $path = $file->getRealPath();
            $data_excel = Excel::load($path, function($reader) {
            })->get();
            if(!empty($data_excel) && $data_excel->count()){

                foreach ($data_excel as $key => $value) {                
                    $updatepersen = TempInsentifModel::updateOrCreate([
                                                            'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                            'EMPLOYEE_NIK' => $value->nik,
                                                            ],
                                                            [
                                                                'TEMP_INCENTIVE_PERCENT_CUT' => $value->potongan*100,
                                                                // 'TEMP_INCENTIVE_PERCENT_CUT' => $value->potongan, // dandy tambah
                                                            ]
                                                         );
                    $updatepersen->save();
                }
            }

            $select_temp = TempInsentifModel::select('tm_employee.EMPLOYEE_NIK','tm_employee.EMPLOYEE_NAME','tm_employee.EMPLOYEE_SLICE',
                                                      'tx_temp_incentive.TEMP_INCENTIVE_TOTAL','tx_temp_incentive.TEMP_INCENTIVE_PERCENT_CUT',
                                                      'tx_temp_incentive.TEMP_INCENTIVE_COST_CUT'
                                                     )
                                             // ->leftjoin('tm_employee','tm_employee.EMPLOYEE_NIK','=','tx_temp_incentive.EMPLOYEE_NIK')
                                                ->leftjoin('tm_employee',function($join){
                                                    $join->on('tm_employee.EMPLOYEE_NIK','=','tx_temp_incentive.EMPLOYEE_NIK')
                                                         ->where('tm_employee.EMPLOYEE_STATUS','=',1);
                                                })
                                             ->where('tx_temp_incentive.INCENTIVE_PARAM_ID',$incentiveparam)
                                             ->orderby('tm_employee.EMPLOYEE_SLICE','ASC')
                                             ->get();

            foreach ($select_temp as $data) {
                $persen_potongan = $data->TEMP_INCENTIVE_PERCENT_CUT; 
                $final = $data->TEMP_INCENTIVE_TOTAL;
                if ($persen_potongan == null OR $persen_potongan == 0.00) {
                    $cost_potongan = 0.000;
                } else {
                    $cost_potongan = $persen_potongan / 100 * $final;
                }

                $updateTemp =  TempInsentifModel::updateOrCreate([
                                                                    'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                                    'EMPLOYEE_NIK' => $data->EMPLOYEE_NIK
                                                                    ],
                                                                    [                                                                
                                                                    'TEMP_INCENTIVE_COST_CUT' => $cost_potongan
                                                                    ]
                                                                 );    
                $updateTemp->save();

            }        

            foreach ($select_temp as $data2) {

                // Dibagi rata

                $total_potongan = TempInsentifModel::select(DB::raw('SUM(TEMP_INCENTIVE_COST_CUT) as total1'))->where('TEMP_INCENTIVE_PERCENT_CUT','>',0)->get();
                $total_incentive_tidakkepotong = TempInsentifModel::select(DB::raw('SUM(TEMP_INCENTIVE_TOTAL) as total2'))->where('TEMP_INCENTIVE_PERCENT_CUT',0)->get();

                
                $total_potongans = $total_potongan[0]->total1;
                $total_incentive_tidakkepotongs = $total_incentive_tidakkepotong[0]->total2;                

                $tambahan = $data2->TEMP_INCENTIVE_TOTAL / $total_incentive_tidakkepotongs * $total_potongans;
                $potongan = $data2->TEMP_INCENTIVE_COST_CUT;

                if ($data2->TEMP_INCENTIVE_PERCENT_CUT > 0) {
                    $simpan_tambahan = 0;
                    $finalfinal = $data2->TEMP_INCENTIVE_TOTAL - $potongan;
                } else {
                    $simpan_tambahan = $tambahan;
                    $finalfinal = $data2->TEMP_INCENTIVE_TOTAL + $tambahan;
                }            

                $updateTemp2 =  TempInsentifModel::updateOrCreate([
                                                                    'INCENTIVE_PARAM_ID' => $incentiveparam,
                                                                    'EMPLOYEE_NIK' => $data2->EMPLOYEE_NIK
                                                                    ],
                                                                    [                                                                
                                                                    'TEMP_INCENTIVE_DIST_CUT' => $simpan_tambahan,    
                                                                    'TEMP_INCENTIVE_TOTAL_AFTER_CUT' => $finalfinal
                                                                    ]
                                                                 );    
                $updateTemp2->save();
                
            }

            return 'success';
 
        }else {
            return 'formatsalah';
        }

        // $new_doc_name = "EXCELPOTONGAN-".$bulan.'-'.$tahun.'-'.$tgl.$jam.".".$doc_extension;

        // if(!$file->move($destinationPath, $new_doc_name)) {
        //     return 'error';
        // }else{
            
        // }
    }

    public function download_template($param="")
    {        
        $bulantahun = explode('-', $param);
        $bulan = $bulantahun[0];
        $tahun = $bulantahun[1];        

        $incentiveparamlast = IncentiveParamModel::select('INCENTIVE_PARAM_ID',
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
                                                ->where('INCENTIVE_PARAM_MONTH',$bulan)
                                                ->where('INCENTIVE_PARAM_YEAR',$tahun)
                                                ->orderby('INCENTIVE_PARAM_ID','DESC')
                                                ->first();
        $incentiveparam = $incentiveparamlast->INCENTIVE_PARAM_ID;
        $incentive_month = $incentiveparamlast->INCENTIVE_PARAM_MONTH_NAME;

        $datas = "SELECT a.EMPLOYEE_NIK, a.EMPLOYEE_NAME, a.EMPLOYEE_SLICE,
                    (SELECT SLICE_NAME FROM tr_slice WHERE SLICE_ID = a.EMPLOYEE_SLICE) as SLICE_KAR,
                    CASE WHEN b.TEMP_INCENTIVE_TOTAL IS NULL THEN '0' ELSE b.TEMP_INCENTIVE_TOTAL END as TEMP_INCENTIVE_TOTAL,
                    CASE WHEN b.TEMP_INCENTIVE_TOTAL IS NULL THEN '0' ELSE b.TEMP_INCENTIVE_TOTAL END as TEMP_INCENTIVE_TOTAL_BERSIH,
                    CASE WHEN b.TEMP_INCENTIVE_COST_CUT IS NULL THEN '0' ELSE b.TEMP_INCENTIVE_COST_CUT END as TEMP_INCENTIVE_COST_CUT,
                    CASE WHEN b.TEMP_INCENTIVE_COST_CUT IS NULL THEN '0' ELSE b.TEMP_INCENTIVE_COST_CUT END as TEMP_INCENTIVE_COST_CUT_ROUND,
                    (CASE 
                      WHEN b.TEMP_INCENTIVE_PERCENT_CUT IS NULL THEN 0
                      ELSE b.TEMP_INCENTIVE_PERCENT_CUT
                    END) AS TEMP_INCENTIVE_PERCENT_CUT
                    FROM (SELECT EMPLOYEE_NIK, TEMP_INCENTIVE_PERCENT_CUT, TEMP_INCENTIVE_TOTAL, TEMP_INCENTIVE_COST_CUT FROM tx_temp_incentive WHERE INCENTIVE_PARAM_ID = '".$incentiveparam."') b
                    LEFT JOIN tm_employee AS a
                    ON a.EMPLOYEE_NIK = b.EMPLOYEE_NIK AND a.EMPLOYEE_STATUS = 1
                    ORDER BY a.EMPLOYEE_SLICE ASC";

        $data = DB::select($datas);

        Excel::create('Potongan Karyawan '.$incentive_month.' '.$tahun, function($excel) use($data) {
            $excel->sheet('Potongan Karyawan', function($sheet) use($data) {
              $sheet->loadView('excel/potongan_karyawan')                
                ->with("data",$data);
            });
        })->export('xls');
    }

    public function getqccrtgmin()
    {
        $data = AdjustParamModel::select('AP_COST_BOX_QCC','AP_COST_BOX_RTG')->first();
        return Response::json($data);
    }

    public function exportSlip($id)
    {
        $showPosting = PostingIncentiveModel::select(
                                    'tx_posting_incentive.POSTING_INCENTIVE_NIK','tx_posting_incentive.POSTING_INCENTIVE_NAME','tm_position.POSITION_NAME',
                                    'tm_group.GROUP_NAME','tr_shift.SHIFT_NAME','tr_block.BLOCK_NAME','tm_division.DIVISION_NAME','tr_slice.SLICE_NAME',
                                    'tx_posting_incentive.POSTING_INCENTIVE_BLOCK', 'tx_posting_incentive.POSTING_INCENTIVE_ID', 'tx_incentive_param.INCENTIVE_PARAM_YEAR',
                                    DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_FLAT),0,"de_DE")) as POSTING_INCENTIVE_FLAT'),
                                    DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_BOX),0,"de_DE")) as POSTING_INCENTIVE_BOX'),
                                    DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_OJEKER),0,"de_DE")) as POSTING_INCENTIVE_OJEKER'),
                                    DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_THRESHOLD),0,"de_DE")) as POSTING_INCENTIVE_THRESHOLD'),
                                    DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_FINAL),0,"de_DE")) as POSTING_INCENTIVE_FINAL'),
                                    'tx_box_adjusment_emp.BAE_BOX_HANDLE',
                                    'tx_posting_incentive.POSTING_INCENTIVE_PERCENT_CUT',
                                    DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_COST_CUT),0,"de_DE")) as POSTING_INCENTIVE_COST_CUT'),
                                    DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_DIST_CUT),0,"de_DE")) as POSTING_INCENTIVE_DIST_CUT'),
                                    DB::raw('CONCAT("Rp. ", FORMAT(ROUND(POSTING_INCENTIVE_FINAL_AFTER_CUT),0,"de_DE")) as POSTING_INCENTIVE_FINAL_AFTER_CUT'),
                                    DB::raw('CASE 
                                        WHEN tx_incentive_param.INCENTIVE_PARAM_MONTH = 1 THEN "Januari"
                                        WHEN tx_incentive_param.INCENTIVE_PARAM_MONTH = 2 THEN "Februari"
                                        WHEN tx_incentive_param.INCENTIVE_PARAM_MONTH = 3 THEN "Maret"
                                        WHEN tx_incentive_param.INCENTIVE_PARAM_MONTH = 4 THEN "April"
                                        WHEN tx_incentive_param.INCENTIVE_PARAM_MONTH = 5 THEN "Mei"
                                        WHEN tx_incentive_param.INCENTIVE_PARAM_MONTH = 6 THEN "Juni"
                                        WHEN tx_incentive_param.INCENTIVE_PARAM_MONTH = 7 THEN "Juli"
                                        WHEN tx_incentive_param.INCENTIVE_PARAM_MONTH = 8 THEN "Agustus"
                                        WHEN tx_incentive_param.INCENTIVE_PARAM_MONTH = 9 THEN "September"
                                        WHEN tx_incentive_param.INCENTIVE_PARAM_MONTH = 10 THEN "Oktober"
                                        WHEN tx_incentive_param.INCENTIVE_PARAM_MONTH = 11 THEN "Nopember"
                                        WHEN tx_incentive_param.INCENTIVE_PARAM_MONTH = 12 THEN "Desember"
                                    END as INCENTIVE_PARAM_MONTH_NAME'),
                                    DB::raw('CONCAT("Rp. ", FORMAT((ROUND(POSTING_INCENTIVE_FLAT) + ROUND(POSTING_INCENTIVE_BOX) + ROUND(POSTING_INCENTIVE_OJEKER) + 
                                        ROUND(POSTING_INCENTIVE_THRESHOLD) + ROUND(POSTING_INCENTIVE_DIST_CUT)),0,"de_DE")) as total_penghasilan'),
                                    DB::raw('CONCAT("Rp. ", FORMAT((ROUND(POSTING_INCENTIVE_FLAT) + ROUND(POSTING_INCENTIVE_BOX) + ROUND(POSTING_INCENTIVE_OJEKER) + 
                                        ROUND(POSTING_INCENTIVE_THRESHOLD) + ROUND(POSTING_INCENTIVE_DIST_CUT)) - ROUND(POSTING_INCENTIVE_COST_CUT),0,"de_DE")) as final')
                                )
                        ->leftjoin('tx_box_adjusment_emp',function($join) {
                            $join->on('tx_posting_incentive.POSTING_INCENTIVE_NIK','=','tx_box_adjusment_emp.EMPLOYEE_NIK');
                            $join->on('tx_box_adjusment_emp.INCENTIVE_PARAM_ID','=','tx_posting_incentive.INCENTIVE_PARAM_ID');
                        })
                        ->leftjoin('tm_position','tm_position.POSITION_ID','=','tx_posting_incentive.POSTING_INCENTIVE_POS')
                        ->leftjoin('tm_group','tm_group.GROUP_ID','=','tx_posting_incentive.POSTING_INCENTIVE_GROUP')
                        ->leftjoin('tr_shift','tr_shift.SHIFT_ID','=','tx_posting_incentive.POSTING_INCENTIVE_SHIFT')
                        ->leftjoin('tr_block','tr_block.BLOCK_ID','=','tx_posting_incentive.POSTING_INCENTIVE_BLOCK')
                        ->leftjoin('tm_division','tm_division.DIVISION_ID','=','tx_posting_incentive.POSTING_INCENTIVE_DIV')
                        ->leftjoin('tr_slice','tr_slice.SLICE_ID','=','POSTING_INCENTIVE_SLICE')
                        ->leftjoin('tx_incentive_param','tx_incentive_param.INCENTIVE_PARAM_ID','=','tx_posting_incentive.INCENTIVE_PARAM_ID')
                        ->where('tx_posting_incentive.POSTING_INCENTIVE_ID',$id)
                        // ->whereRaw("tx_posting_incentive.POSTING_INCENTIVE_NIK IN ('K980424', 'K980368', 'K010568', 'K010570', 'K970076', 'K970084', 'K980456', 'K970153', 'K070602', 'K970056', 'K970151', 'K980340')")
                        ->first();
        
        $pdf = PDF::loadView('pdf.slip',compact('showPosting'))
	                ->setPaper('a4','landscape');
	 
	    return $pdf->stream();
    }
}
