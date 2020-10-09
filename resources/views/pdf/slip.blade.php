<style>
    body {
        font-size:13px;
    }
</style>
<body>
<center>
    <h2>Terminal Peti Kemas Koja</h2>
    <h4>Jl. Timor Raya, RW.1, Koja, Kec. Koja, Kota Jkt Utara, Daerah Khusus Ibukota Jakarta 14220</h4>
</center>
<hr>
<center>
    <strong>
        <p><u>SLIP INSENTIF KARYAWAN</u></p>
        <p>( {{ $showPosting->INCENTIVE_PARAM_MONTH_NAME.' '.$showPosting->INCENTIVE_PARAM_YEAR }} )</p>
    </strong>
</center>
<table>
    <tr>
        <td width="500px">
            <table>
                <tr>
                    <td width="150px">NIK</td>
                    <td width="50px">:</td>
                    <td>{{ $showPosting->POSTING_INCENTIVE_NIK }}</td>
                </tr>
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td>{{ $showPosting->POSTING_INCENTIVE_NAME }}</td>
                </tr>
                <tr>
                    <td>Position / Group</td>
                    <td>:</td>
                    <td>{{ $showPosting->POSITION_NAME.' / '.$showPosting->GROUP_NAME }}</td>
                </tr>
                <tr>
                    <td>Shift</td>
                    <td>:</td>
                    <td>{{ $showPosting->SHIFT_NAME }}</td>
                </tr>
            </table>   
        </td>
        <td>
            <table>
                <tr>
                    <td width="150px">Block</td>
                    <td width="50px">:</td>
                    <td>{{ $showPosting->BLOCK_NAME }}</td>
                </tr>
                <tr>
                    <td>Divisi</td>
                    <td>:</td>
                    <td>{{ $showPosting->DIVISION_NAME }}</td>
                </tr>
                <tr>
                    <td>Slice</td>
                    <td>:</td>
                    <td>{{ $showPosting->SLICE_NAME }}</td>
                </tr>
                <tr>
                    <td>Box Handle</td>
                    <td>:</td>
                    <td>{{ $showPosting->BAE_BOX_HANDLE }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br>
<table>
    <tr>
        <td width="500px;">
            <table>
                <tr>
                    <td colspan="3"><u>PENGHASILAN</u></td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150px">Incentive Flat</td>
                    <td width="50px">=</td>
                    <td width="100px">{{ $showPosting->POSTING_INCENTIVE_FLAT }}</td>
                </tr>
                <tr>
                    <td>Incentive Block</td>
                    <td>=</td>
                    <td>{{ $showPosting->POSTING_INCENTIVE_BOX }}</td>
                </tr>
                <tr>
                    <td>Incentive Ojeker</td>
                    <td>=</td>
                    <td>{{ $showPosting->POSTING_INCENTIVE_OJEKER }}</td>
                </tr>
                <tr>
                    <td>Incentive Threshold</td>
                    <td>=</td>
                    <td>{{ $showPosting->POSTING_INCENTIVE_THRESHOLD }}</td>
                </tr>
                <tr>
                    <td>Distribution Potongan</td>
                    <td>=</td>
                    <td>{{ $showPosting->POSTING_INCENTIVE_DIST_CUT }}</td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td><strong>Total (A)</strong></td>
                    <td><strong>=</strong></td>
                    <td><strong>{{ $showPosting->total_penghasilan }}</strong></td>
                </tr>   
            </table>
        </td>
        <td>
            <table>
                <tr>
                    <td colspan="3"><u>POTONGAN</u></td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150px">Potongan (%)</td>
                    <td width="50px">=</td>
                    <td width="100px">{{ $showPosting->POSTING_INCENTIVE_PERCENT_CUT }}</td>
                </tr>
                <tr>
                    <td>Cost Potongan</td>
                    <td>=</td>
                    <td>{{ $showPosting->POSTING_INCENTIVE_DIST_CUT }}</td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td><strong>Total (B)</strong></td>
                    <td><strong>=</strong></td>
                    <td><strong>{{ $showPosting->POSTING_INCENTIVE_DIST_CUT }}</strong></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br>
<table>
    <tr style="background-color:#dedbe6">
        <td width="500px"><strong>PENERIMAAN BERSIH (A-B)</strong></td>
        <td width="70px"><strong>=</strong></td>
        <td width="400px"><strong>{{ $showPosting->final }}</strong></td>
    </tr>
</table>
<br>
<table style="margin-left:800px;">
    <tr>
        <td>Jakarta, {{ date('d m Y') }}</td>
    </tr>
    <tr>
        <td>Manager HRD</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>Nama Manager HRD</td>
    </tr>
</table>
</body>