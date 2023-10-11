<!DOCTYPE html>
<html lang="en">
<head>
    <title>Feedback Summary Report</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <style>
        html
        {
            margin: 25px;
        }
        *
        {
            font-family: Arial, Helvetica, sans-serif;
        }
        .h3
        {
            font-size: 1.5em;
            text-transform: uppercase;
        }
        .h4
        {
            font-size: 1.2em;
            text-transform: uppercase;
        }
        .h5
        {
            font-size: 1em;
            text-transform: uppercase;
        }
        table {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        table td, table th {
            border: 1px solid #ddd;
            padding: 3px;
        }

        table tr:nth-child(even){
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #ddd;
        }

        table th {
            padding-top: 5px;
            padding-bottom: 5px;
            text-align: left;
            background-color: #9E9E9E;
            color: white;
        }
        .footer {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            background-color: transparent;
            color: black;
            z-index: 999;
        }
    </style>
</head>
<body>
    <div style="padding: 25px 25px 0px 25px;">
        <div style="text-align: center;">
            <img src="https://www.sanfernandocity.gov.ph/csflu_website/wp-content/uploads/2020/04/San-Fernando-Seal_1-500x500.jpg" width="75" height="75" style="border-bottom: 15px;" />
            <div class="h4">Feedback Summary Report</div>
            <div class="h5">{{ $date }}</div>
        </div>
        <div style="padding: 25px 0px 0px 0px;">
            <table>
                <tr>
                    <th colspan="2"><div style="text-transform: uppercase;">feedback</div></th>
                </tr>
                <tr>
                    <td style="width: 70%;"><div style="text-transform: uppercase;">total feedback</div></td>
                    <td style="text-align: center;">{{ $totalFeedback }}</td>
                </tr>
                @foreach ($levelArr as $lev)
                <tr>
                    <td style="width: 70%;"><div style="text-transform: uppercase;">{{ $lev['label'] }}</div></td>
                    <td style="text-align: center;">{{ $lev['count'] }}</td>
                </tr>
                @endforeach
                <tr>
                    <td style="width: 70%;"><div style="text-transform: uppercase;">overall rating</div></td>
                    <td style="text-align: center;">{{ $or }} %</td>
                </tr>
                <tr>
                    <td style="width: 70%;"><div style="text-transform: uppercase;">feedback rating</div></td>
                    <td style="text-align: center;">{{ $fr }} %</td>
                </tr>
                <tr>
                    <td colspan="2"><div style="text-transform: uppercase;">kiosk rating</div></td>
                </tr>
                <tr>
                    <td style="width: 70%;">
                        <div style="text-transform: uppercase; font-size: .85em; font-weight: bold;">physical</div>
                        <div style="text-transform: uppercase; font-size: .85em;">The work environment is clean & orderly</div>
                    </td>
                    <td style="text-align: center;">{{ $kr_phy }} %</td>
                </tr>
                <tr>
                    <td style="width: 70%;">
                        <div style="text-transform: uppercase; font-size: .85em; font-weight: bold;">services</div>
                        <div style="text-transform: uppercase; font-size: .85em;">Your concern is addressed promptly & appropriately</div>
                    </td>
                    <td style="text-align: center;">{{ $kr_ser }} %</td>
                </tr>
                <tr>
                    <td style="width: 70%;">
                        <div style="text-transform: uppercase; font-size: .85em; font-weight: bold;">personnel</div>
                        <div style="text-transform: uppercase; font-size: .85em;">The employee was courteous & accomodating</div>
                    </td>
                    <td style="text-align: center;">{{ $kr_per }} %</td>
                </tr>
                <tr>
                    <td style="width: 70%;">
                        <div style="text-transform: uppercase; font-size: .85em; font-weight: bold;">overall</div>
                        <div style="text-transform: uppercase; font-size: .85em;">How satisfied are you with the quality of service?</div>
                    </td>
                    <td style="text-align: center;">{{ $kr_ovr }} %</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="footer">
        <div style="text-transform: uppercase; font-size: .65em; font-style: italic;">generated by: eParticipation system | {{ $now }}</div>
    </div>
</body>
</html>