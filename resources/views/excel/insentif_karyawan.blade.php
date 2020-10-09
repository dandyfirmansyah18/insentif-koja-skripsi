<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />    
</head>
<body> 

<h3 colspan="18" align="left" >Karyawan Insentif {{$bulan}} Tahun {{$tahun}} </h1>

<table >
    <thead>
    <tr>
        <td>&nbsp;</td>  
    </tr>
    </thead>
</table>

<table style="border: 1px solid black;">
    
    <tr>        
        <th width="6" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">NO.</th>
        <th width="12" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">EMPLOYEE NIK</th>
        <th width="35" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">EMPLOYEE NAME</th>
        <th width="20" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">POSITION</th>
        <th width="15" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">GROUP</th>
        <th width="15" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">SHIFT</th>
        <th width="15" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">BLOCK</th>
        <th width="15" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">SLICE</th>
        <th width="15" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">BOX HANDLE</th>
        <th width="25" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">INCENTIVE-FLAT</th>
        <th width="25" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">INCENTIVE-BOX</th>
        <th width="25" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">INCENTIVE-OJEKER</th>
        <th width="25" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">INCENTIVE-THRESHOLD</th>
        <th width="15" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">INCENTIVE-TOTAL</th>
        <th width="25" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">(%) POTONGAN</th>
        <th width="25" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">JUMLAH POTONGAN</th>
        <th width="25" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">PENGEMBALIAN INSENTIF</th>
        <th width="25" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">INCENTIVE-FINAL</th>
    </tr>



    <?php $z=0; ?>
    @foreach($data as $datas)
    <?php
        
        $z++;
    ?>
    <tr>

        <td style="border: 3px solid #000000;">{{ $z }}</td>        
        <td style="border: 3px solid #000000;">{{$datas->EMPLOYEE_NIK}}</td>
        <td style="border: 3px solid #000000;">{{ $datas->EMPLOYEE_NAME }}</td>
        <td style="border: 3px solid #000000;">{{ $datas->POSITION_NAME }}</td>
        <td style="border: 3px solid #000000;">{{ $datas->GROUP_NAME }}</td>
        <td style="border: 3px solid #000000;">{{ $datas->SHIFT_NAME }}</td>
        <td style="border: 3px solid #000000;">{{ $datas->BLOCK_NAME }}</td>
        <td style="border: 3px solid #000000;">{{ $datas->SLICE_NAME }}</td>
        <td style="border: 3px solid #000000;">{{ $datas->BAE_BOX_HANDLE }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $datas->INCENTIVE_FLAT }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $datas->INCENTIVE_BOX }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $datas->INCENTIVE_OJEKER }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $datas->INCENTIVE_THRESHOLD }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $datas->INCENTIVE_TOTAL }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $datas->INCENTIVE_PERCENT_CUT }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $datas->INCENTIVE_COST_CUT }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $datas->INCENTIVE_DIST_CUT }}</td>        
        <td align="right" style="border: 3px solid #000000;">{{ $datas->INCENTIVE_TOTAL_AFTER_CUT }}</td>        
    </tr>
    @endforeach

    <tr>
        <td style="border: 3px solid #000000;"></td>  
        <td style="border: 3px solid #000000;"></td>        
        <td style="border: 3px solid #000000;"></td>
        <td style="border: 3px solid #000000;"></td>
        <td style="border: 3px solid #000000;"></td>
        <td style="border: 3px solid #000000;"></td>
        <td style="border: 3px solid #000000;"></td>
        <td style="border: 3px solid #000000;"></td>
        <td style="border: 3px solid #000000;"></td>
        <td align="right" style="border: 3px solid #000000;">{{ $sum_incentive[0]->SUM_INCENTIVE_FLAT }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $sum_incentive[0]->SUM_INCENTIVE_BOX }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $sum_incentive[0]->SUM_INCENTIVE_OJEKER }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $sum_incentive[0]->SUM_INCENTIVE_THRESHOLD }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $sum_incentive[0]->SUM_INCENTIVE_TOTAL }}</td>
        <td align="right" style="border: 3px solid #000000;"></td>
        <td align="right" style="border: 3px solid #000000;">{{ $sum_incentive[0]->SUM_INCENTIVE_COST_CUT }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $sum_incentive[0]->SUM_INCENTIVE_DIST_CUT }}</td>        
        <td align="right" style="border: 3px solid #000000;">{{ $sum_incentive[0]->SUM_INCENTIVE_TOTAL_AFTER_CUT }}</td>        
    </tr>

</table> 
</body> 
</html>