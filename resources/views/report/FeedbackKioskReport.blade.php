<!DOCTYPE html>
<html lang="en">
<head>
    <title>Feedback Kiosk Report</title>
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
            <div class="h4">Feedback Kiosk Report</div>
            <div class="h5">{{ $date }}</div>
        </div>
        <div style="padding: 25px 0px 0px 0px;">
            <table>
                <tr>
                    <th rowspan="2" style="width: 30%;"><div style="text-transform: uppercase;">-</div></th>
                    <th colspan="5"><div style="text-transform: uppercase; text-align: center;">kiosk rating</div></th>
                </tr>
                <tr>
                    <th style="text-align: center; width: 10%;">Physical</th>
                    <th style="text-align: center; width: 10%;">Services</th>
                    <th style="text-align: center; width: 10%;">Personnel</th>
                    <th style="text-align: center; width: 10%;">Overall</th>
                    <th style="text-align: center; width: 30%;">Suggestions</th>
                </tr>
                @foreach ($data as $dt)
                <tr>
                    <td>
                        <div style="text-transform: uppercase; font-weight: bold;">{{ $dt['name'] }}</div>
                        <div><span style="text-transform: uppercase;">{{ $dt['office'] }}</span>,&nbsp;<span style="text-transform: capitalize;">{{ $dt['position'] }}</span></div>
                    </td>
                    <td style="text-align: center;">{{ $dt['kr_phy'] }}%</td>
                    <td style="text-align: center;">{{ $dt['kr_ser'] }}%</td>
                    <td style="text-align: center;">{{ $dt['kr_per'] }}%</td>
                    <td style="text-align: center;">{{ $dt['kr_ovr'] }}%</td>
                    <td style="text-align: left;">
                        @foreach ($dt['suggestions'] as $s)
                        <div style="margin-bottom: 3px;">- {{ $s['content'] }}</div>
                        @endforeach
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
    <div class="footer">
        <div style="text-transform: uppercase; font-size: .65em; font-style: italic;">generated by: eParticipation system | {{ $now }}</div>
    </div>
</body>
</html>