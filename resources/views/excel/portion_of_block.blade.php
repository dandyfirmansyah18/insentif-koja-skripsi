<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />    
</head>
<body> 

<h3 colspan="7" align="left">Portion Of Block Insentif {{$bulan}} Tahun {{$tahun}} </h3>

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
        <th width="20" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">Diff Per Block</th>
        <th width="12" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">Headcount per Block</th>
        <th width="12" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">Block</th>
        <th width="15" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">Kue Per Block</th>
        <th width="15" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">Different Nominal</th>
        <th width="15" align="center"  style="text-align:center;background-color:#ECF542; border: 3px solid #000000;">Block Composition</th>        
    </tr>



    <?php $z=0; ?>
    @foreach($showPortionOfBlock as $showPortionOfBlocks)
    <?php
        
        $z++;
    ?>
    <tr>
        <td style="border: 3px solid #000000;">{{ $z }}</td>        
        <td align="right" style="border: 3px solid #000000;">{{ $showPortionOfBlocks->POB_DIFF_PER_BLOCK }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $showPortionOfBlocks->HEADACOUNT_PER_BLOCK }} Orang</td>
        <td align="right" style="border: 3px solid #000000;">{{ $showPortionOfBlocks->BLOCK_ID }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $showPortionOfBlocks->POB_KUE_PER_BLOCK }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $showPortionOfBlocks->POB_DIFF_NOMINAL }}</td>
        <td align="right" style="border: 3px solid #000000;">{{ $showPortionOfBlocks->Block_Composition }} %</td> 
    </tr>
    @endforeach
    

</table> 
</body> 
</html>