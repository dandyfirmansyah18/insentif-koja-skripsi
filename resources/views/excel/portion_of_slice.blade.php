<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />    
</head>
<body> 

<h3 colspan="9" align="left">Portion Of Slice Insentif {{$bulan}} Tahun {{$tahun}} </h3>

<table>
    <thead>
    <tr>
        <td>&nbsp;</td>  
    </tr>
    </thead>
</table>

<table style="border: 1px solid black;">
    
    <tr>        
        <th width="6" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">NO.</th>
        <th width="20" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">Selisih Per Slice</th>
        <th width="12" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">Block</th>
        <th width="12" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">Slice</th>
        <th width="15" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">Jumlah Orang</th>
        <th width="15" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">Coefficient of upper value</th>
        <th width="15" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">Bobot</th>
        <th width="20" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">Simulation Kue Per Slice</th>
        <th width="20" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">Flat Distributed</th>
    </tr>



    <?php $z=0; ?>
    @foreach($showPortionOfSlice as $showPortionOfSlices)
    <?php
        
        $z++;
    ?>
    <tr>

        <td style="border: 3px solid #000000;">{{ $z }}</td>        
        <td align="right" style="border: 3px solid #000000;">{{ $showPortionOfSlices->POS_DIFF_PER_SLICE }} %</td>
        <td align="right" style="border: 3px solid #000000;">{{ $showPortionOfSlices->BLOCK_ID }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $showPortionOfSlices->SLICE_ID }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $showPortionOfSlices->POS_HEADACOUNT_PER_SLICE }} Orang</td>
        <td align="right" style="border: 3px solid #000000;">{{ $showPortionOfSlices->POS_COEFFICIENT_UPPER_VALUE }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $showPortionOfSlices->POS_BOBOT }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $showPortionOfSlices->POS_KUE_PER_SLICE }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $showPortionOfSlices->POS_NOM_FLAT_DIST_BLOCK_I }}</td>    
    </tr>
    @endforeach

    <tr>
        <td style="border: 3px solid #000000;"></td>        
        <td align="right" style="border: 3px solid #000000;"></td>
        <td align="right" style="border: 3px solid #000000;"></td>
        <td align="right" style="border: 3px solid #000000;"></td>
        <td align="right" style="border: 3px solid #000000;"></td>
        <td align="right" style="border: 3px solid #000000;"></td>
        <td align="right" style="border: 3px solid #000000;"></td>
        <td align="right" style="border: 3px solid #000000;">{{ $sum_PortionOfSlice[0]->SUM_POS_KUE_PER_SLICE }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $sum_PortionOfSlice[0]->SUM_POS_NOM_FLAT_DIST_BLOCK_I }}</td>    
    </tr>

</table> 
</body> 
</html>