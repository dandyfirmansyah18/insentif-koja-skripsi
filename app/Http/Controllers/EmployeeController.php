<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Http\Requests;
use App\IncentiveParamModel;
use App\EmployeeModel;
use App\PositionModel;
use App\GroupModel;
use App\DivisiModel;
use App\BlockModel;
use App\ShiftModel;
use App\SliceModel;
use App\TempInsentifModel;
use DB;

class EmployeeController extends Controller
{
    //
    //
    public function index(Request $request)
    {
        if($request->get('cari')){
            $datas = EmployeeModel::select('tm_employee.*','tm_group.GROUP_NAME','tm_position.POSITION_NAME','tr_block.BLOCK_NAME','tr_shift.SHIFT_NAME','tr_slice.SLICE_NAME','tm_division.DIVISION_NAME')
                                    ->leftjoin('tm_group','tm_employee.EMPLOYEE_GROUP','=','tm_group.GROUP_ID')
                                    ->leftjoin('tm_position','tm_employee.EMPLOYEE_POS','=','tm_position.POSITION_ID')
                                    ->leftjoin('tr_block','tm_employee.EMPLOYEE_BLOCK','=','tr_block.BLOCK_ID')
                                    ->leftjoin('tr_shift','tm_employee.EMPLOYEE_SHIFT','=','tr_shift.SHIFT_ID')
                                    ->leftjoin('tr_slice','tm_employee.EMPLOYEE_SLICE','=','tr_slice.SLICE_ID')
                                    ->leftjoin('tm_division','tm_employee.EMPLOYEE_DIV','=','tm_division.DIVISION_ID')
                                    // ->where("tm_employee.EMPLOYEE_NAME", "LIKE", "%{$request->get('cari')}%")
                                    ->where('tm_employee.EMPLOYEE_NAME','like','%' . $request->get('cari') . '%')
                                    ->orWhere('tm_employee.EMPLOYEE_NIK','like','%' . $request->get('cari') . '%')
                                    ->orWhere('tr_block.BLOCK_NAME','like','%' . $request->get('cari') . '%')
                                    ->orWhere('tr_slice.SLICE_NAME','like','%' . $request->get('cari') . '%')
                                    ->orWhere('tm_position.POSITION_NAME','like','%' . $request->get('cari') . '%')
                                    ->paginate(10);
        }else{
            $datas = EmployeeModel::select('tm_employee.*','tm_group.GROUP_NAME','tm_position.POSITION_NAME','tr_block.BLOCK_NAME','tr_shift.SHIFT_NAME','tr_slice.SLICE_NAME','tm_division.DIVISION_NAME')
                                    ->leftjoin('tm_group','tm_employee.EMPLOYEE_GROUP','=','tm_group.GROUP_ID')
                                    ->leftjoin('tm_position','tm_employee.EMPLOYEE_POS','=','tm_position.POSITION_ID')
                                    ->leftjoin('tr_block','tm_employee.EMPLOYEE_BLOCK','=','tr_block.BLOCK_ID')
                                    ->leftjoin('tr_shift','tm_employee.EMPLOYEE_SHIFT','=','tr_shift.SHIFT_ID')
                                    ->leftjoin('tr_slice','tm_employee.EMPLOYEE_SLICE','=','tr_slice.SLICE_ID')
                                    ->leftjoin('tm_division','tm_employee.EMPLOYEE_DIV','=','tm_division.DIVISION_ID')
                                    ->paginate(10);
        }
        return response($datas);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $create = EmployeeModel::create($input);
        $full = EmployeeModel::select('tm_employee.*','tm_group.GROUP_NAME','tm_position.POSITION_NAME','tr_block.BLOCK_NAME','tr_shift.SHIFT_NAME','tr_slice.SLICE_NAME','tm_division.DIVISION_NAME')
                                    ->leftjoin('tm_group','tm_employee.EMPLOYEE_GROUP','=','tm_group.GROUP_ID')
                                    ->leftjoin('tm_position','tm_employee.EMPLOYEE_POS','=','tm_position.POSITION_ID')
                                    ->leftjoin('tr_block','tm_employee.EMPLOYEE_BLOCK','=','tr_block.BLOCK_ID')
                                    ->leftjoin('tr_shift','tm_employee.EMPLOYEE_SHIFT','=','tr_shift.SHIFT_ID')
                                    ->leftjoin('tr_slice','tm_employee.EMPLOYEE_SLICE','=','tr_slice.SLICE_ID')
                                    ->leftjoin('tm_division','tm_employee.EMPLOYEE_DIV','=','tm_division.DIVISION_ID')
                                    ->where("tm_employee.EMPLOYEE_NIK",$request->get('EMPLOYEE_NIK'))
                                    ->first();
        if ($create) {
            return Response::json(array('data' => $full , 'success' => true));
        } else {
            return Response::json(array('data' => $full , 'success' => false));
        }
    }

    public function edit($id)
    {
        $data = EmployeeModel::find($id);  
        // $sliceket = SliceModel::select(DB::raw('CONCAT(SLICE_ID," - ",SLICE_NAME) as EMPLOYEE_SLICE_KETERANGAN'))->where('SLICE_ID',$data['EMPLOYEE_SLICE'])->first();
        // $sliceket = $sliceket->EMPLOYEE_SLICE_KETERANGAN;

        // $data['EMPLOYEE_SLICE_KETERANGAN'] = $sliceket;        

        return response($data);        
    }

    public function update(Request $request,$id)
    {
        $last_id_param = IncentiveParamModel::select('INCENTIVE_PARAM_ID')->where('INCENTIVE_PARAM_STATUS','<',6)->orderby('INCENTIVE_PARAM_ID','DESC')->get();
        $count_param = count($last_id_param);

        if ($count_param > 0) {            
            $last_id = $last_id_param[0]->INCENTIVE_PARAM_ID;
        }else{
            $last_id = '';
        }

        $input = $request->all();
        unset($input['EMPLOYEE_SLICE_KETERANGAN']);        

        $update = EmployeeModel::where("EMPLOYEE_NIK",$id)->update($input);

        //update step become 1 again 
        if ($count_param > 0) {
            $updateParam = DB::table('tx_incentive_param')
                            ->where('INCENTIVE_PARAM_ID', $last_id)
                            ->update(['INCENTIVE_PARAM_STATUS' => 1, 'INCENTIVE_PARAM_CALCULATE_AGAIN' => 1]);

            $delete_temp = TempInsentifModel::query()->truncate(); 
        }

        $data = EmployeeModel::select('tm_employee.*','tm_group.GROUP_NAME','tm_position.POSITION_NAME','tr_block.BLOCK_NAME','tr_shift.SHIFT_NAME','tr_slice.SLICE_NAME','tm_division.DIVISION_NAME')
                                    ->leftjoin('tm_group','tm_employee.EMPLOYEE_GROUP','=','tm_group.GROUP_ID')
                                    ->leftjoin('tm_position','tm_employee.EMPLOYEE_POS','=','tm_position.POSITION_ID')
                                    ->leftjoin('tr_block','tm_employee.EMPLOYEE_BLOCK','=','tr_block.BLOCK_ID')
                                    ->leftjoin('tr_shift','tm_employee.EMPLOYEE_SHIFT','=','tr_shift.SHIFT_ID')
                                    ->leftjoin('tr_slice','tm_employee.EMPLOYEE_SLICE','=','tr_slice.SLICE_ID')
                                    ->leftjoin('tm_division','tm_employee.EMPLOYEE_DIV','=','tm_division.DIVISION_ID')->find($id);
        if ($update) {
            return Response::json(array('data' => $data , 'success' => true));
        }else{
            return Response::json(array('data' => $data , 'success' => false));
        }        
    }

    public function destroy($id)
    {
        return EmployeeModel::where('EMPLOYEE_NIK',$id)->delete();
    }

    public function getPosisi(){
        $data = PositionModel::select('*')->get();
        return response($data);
    }

    public function getGroup(){
        $data = GroupModel::select('*')->get();
        return response($data);
    }

    public function getShift(){
        $data = ShiftModel::select('*')->get();
        return response($data);
    }

    public function getBlock(){
        $data = BlockModel::select('*')->get();
        return response($data);
    }

    public function getSlice(){
        $data = SliceModel::select('*')->get();
        return response($data);
    }

    public function getDivisi(){
        $data = DivisiModel::select('*')->get();
        return response($data);
    }

    public function getCountEmployee(){
        $data = EmployeeModel::select('*')->count();
        return response($data);
        // return $data;
    }

    public function getGrade(Request $request)
    {
        $blockshift = $request->get('value');
        $blockshift = explode("-", $blockshift);

        $block = $blockshift[0];
        $shift = $blockshift[1];

        if ($block == '4') {
            $shift = '2';
        }

        $data = SliceModel::select('*')->where('SLICE_BLOCK',$block)->where('SLICE_SHIFT',$shift)->get();
        return response($data);
    }

    public function checkSlice(Request $request){
        $value = $request->get('value');
        $value = explode("-", $value);

        $data = SliceModel::select('*')->where('SLICE_BLOCK',$value[1])->where('SLICE_SHIFT',$value[2])->where('GRADE',$value[0])->first();
        return response($data);
    }
}
