<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />    
</head>
<body> 
<table style="border: 1px solid black;">
    
    <tr>        
        <th width="6" align="left"  style="text-align:left;background-color:#ECF542; border: 3px solid #000000;">no</th>
        <th width="20" align="left"  style="text-align:left;background-color:#ECF542; border: 3px solid #000000;">nik</th>
        <th width="35" align="left"  style="text-align:left;background-color:#ECF542; border: 3px solid #000000;">nama</th>
        <th width="15" align="left"  style="text-align:left;background-color:#ECF542; border: 3px solid #000000;">total_insentif</th>
        <th width="12" align="left"  style="text-align:left;background-color:#ECF542; border: 3px solid #000000;">potongan</th>        
    </tr>

    <?php $z=0; ?>
    @foreach($data as $datas)
    <?php
        $z++;
    ?>
    <tr>
        <td style="border: 3px solid #000000;">{{ $z }}</td>        
        <td align="right" style="border: 3px solid #000000;">{{ $datas->EMPLOYEE_NIK }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $datas->EMPLOYEE_NAME }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $datas->TEMP_INCENTIVE_TOTAL }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $datas->TEMP_INCENTIVE_PERCENT_CUT }}</td>
    </tr>
    @endforeach
    
</table> 
</body> 
</html>