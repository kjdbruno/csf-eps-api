<!DOCTYPE html>
<html lang="en">
<head>
    <title>Performance Report</title>
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
    <div style="padding: 25px 25px 30px 25px;">
        <div style="text-align: center;">
            <img src="https://www.sanfernandocity.gov.ph/csflu_website/wp-content/uploads/2020/04/San-Fernando-Seal_1-500x500.jpg" width="75" height="75" style="border-bottom: 15px;" />
            <div class="h4">Perfomance Report</div>
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
                        <div style="text-transform: uppercase; font-size: .85em;">office environment/atmosphere</div>
                    </td>
                    <td style="text-align: center;">{{ $kr_phy }} %</td>
                </tr>
                <tr>
                    <td style="width: 70%;">
                        <div style="text-transform: uppercase; font-size: .85em;">treatment of employees towards you</div>
                    </td>
                    <td style="text-align: center;">{{ $kr_ser }} %</td>
                </tr>
                <tr>
                    <td style="width: 70%;">
                        <div style="text-transform: uppercase; font-size: .85em;">promptness of requests as for the services we provide</div>
                    </td>
                    <td style="text-align: center;">{{ $kr_per }} %</td>
                </tr>
                <tr>
                    <td style="width: 70%;">
                        <div style="text-transform: uppercase; font-size: .85em;">overall experience</div>
                    </td>
                    <td style="text-align: center;">{{ $kr_ovr }} %</td>
                </tr>
            </table>
        </div>
        <div style="padding: 25px 0px 0px 0px; page-break-before: always;">
            <table>
                <thead>
                    <th style="width: 40%;"><div style="text-transform: uppercase;">Per category</div></th>
                    <th style="width: 15%;"><div style="text-transform: uppercase; text-align: center;">pending</div></th>
                    <th style="width: 15%;"><div style="text-transform: uppercase; text-align: center;">ongoing</div></th>
                    <th style="width: 15%;"><div style="text-transform: uppercase; text-align: center;">completed</div></th>
                    <th style="width: 15%;"><div style="text-transform: uppercase; text-align: center;">total</div></th>
                </thead>
                @foreach ($catArr as $cat)
                <tr>
                    <td><div style="text-transform: uppercase;">{{ $cat['label'] }}</div></td>
                    <td style="text-align: center;">{{ $cat['pf'] }}</td>
                    <td style="text-align: center;">{{ $cat['of'] }}</td>
                    <td style="text-align: center;">{{ $cat['cf'] }}</td>
                    <td style="text-align: center;">{{ $cat['tf'] }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        <div style="padding: 25px 0px 0px 0px; page-break-before: always;">
            <table>
                <thead>
                    <th style="width: 70%;"><div style="text-transform: uppercase;">Per office</div></th>
                    <th style="width: 30%;"><div style="text-transform: uppercase; text-align: center;">overall rating</div></th>
                </thead>
                @foreach ($offArr as $off)
                <tr>
                    <td><div style="text-transform: uppercase;">{{ $off['label'] }}</div></td>
                    <td style="text-align: center;">{{ $off['rating'] }}&nbsp;%</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
    <div class="footer">
        <div style="text-transform: uppercase; font-size: .5em; font-style: italic;">generated by:</div>
        <div style="text-transform: uppercase; font-size: .5em; font-style: italic;">{{ $name }}</div>
        <div style="text-transform: uppercase; font-size: .5em; font-style: italic;">{{ $now }}</div>
        <div style="text-transform: uppercase; font-size: .5em; font-style: italic; right: 0; top: 0; position: absolute;">eParticipation System</div>
    </div>
</body>
</html>