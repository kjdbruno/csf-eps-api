<!DOCTYPE html>
<html lang="en">
<head>
    <title>Kiosk Detail</title>
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
            <div class="h4">Kiosk Report</div>
        </div>
        @if (!$kiosks->isEmpty())
        <div style="padding: 25px 0px 0px 0px;">
            <table>
                <tr>
                    <td colspan="4"><div style="text-transform: uppercase; text-weight: bold;">basic information</div></td>
                </tr>
                @foreach($kiosks as $dt)
                <tr>
                    <td width="25%"><div style="text-transform: uppercase; text-weight: bold;">personnel</div></td>
                    <td width="75%"><div style="font-size: .85em; text-transform: capitalize;">{{ $dt['name'] }}</div></td>
                </tr>
                <tr><td width="25%"><div style="text-transform: uppercase; text-weight: bold;">office</div></td>
                    <td width="75%"><div style="font-size: .85em; text-transform: capitalize;">{{ $dt['office'] }}</div></td>
                </tr>
                <tr>
                    <td width="25%"><div style="text-transform: uppercase; text-weight: bold;">client's name</div></td>
                    <td width="75%"><div style="font-size: .85em; text-transform: capitalize;">{{ $dt['complainant'] }}</div></td>
                </tr>
                <tr>
                    <td width="25%"><div style="text-transform: uppercase; text-weight: bold;">contact no.</div></td>
                    <td width="75%"><div style="font-size: .85em; text-transform: capitalize;">{{ $dt['number'] }}</div></td>
                </tr>
                <tr>
                    <td width="25%"><div style="text-transform: uppercase; text-weight: bold;">e-mail</div></td>
                    <td width="75%"><div style="font-size: .85em; text-transform: capitalize;">{{ $dt['email'] }}</div></td>
                </tr>
                <tr>
                    <td width="25%"><div style="text-transform: uppercase; text-weight: bold;">date</div></td>
                    <td width="75%"><div style="font-size: .85em;">{{ $list[0]->created_at->format('F d, Y') }}</div></td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif
        <div style="padding: 25px 0px 0px 0px;">
            <table>
                <tr>
                    <td colspan="4"><div style="text-transform: uppercase; text-weight: bold;">satisfaction rating</div></td>
                </tr>
                <tr>
                    <td width="40%"><div style="text-transform: uppercase; text-weight: bold;">-</div></td>
                    <td width="20%"><div style="text-transform: uppercase; text-weight: bold; text-align: center;">dissatisfied</div></td>
                    <td width="20%"><div style="text-transform: uppercase; text-weight: bold; text-align: center;">neutral</div></td>
                    <td width="20%"><div style="text-transform: uppercase; text-weight: bold; text-align: center;">satisfied</div></td>
                </tr>
                <tr>
                    <td width="40%">
                        <div style="text-transform: uppercase; text-weight: bold;">
                            <div>I. PHYSICAL</div>
                            <div style='font-size: .65em;'>The work environment is clean and orderly</div>
                        </div>
                    </td>
                    <td width="20%">
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->phyRating ==1)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td width="20%">
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->phyRating ==2)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td width="20%">
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->phyRating ==3)
                                &times;
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="40%">
                        <div style="text-transform: uppercase; text-weight: bold;">
                            <div>II. SERVICES</div>
                            <div style='font-size: .65em;'>Your concern is addressed promptly and appropriate</div>
                        </div>
                    </td>
                    <td width="20%">
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->serRating ==1)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td width="20%">
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->serRating ==2)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td width="20%">
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->serRating ==3)
                                &times;
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="40%">
                        <div style="text-transform: uppercase; text-weight: bold;">
                            <div>III. PERSONNEL</div>
                            <div style='font-size: .65em;'>The employee was courteous and accomodating</div>
                        </div>
                    </td>
                    <td width="20%">
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->perRating ==1)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td width="20%">
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->perRating ==2)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td width="20%">
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->perRating ==3)
                                &times;
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="40%">
                        <div style="text-transform: uppercase; text-weight: bold;">
                            <div>IV. OVERALL RATING</div>
                            <div style='font-size: .65em;'>How satisfied are you with the quality of service provided</div>
                        </div>
                    </td>
                    <td width="20%">
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->ovrRating ==1)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td width="20%">
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->ovrRating ==2)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td width="20%">
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->ovrRating ==3)
                                &times;
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div style="padding: 25px 0px 0px 0px;">
            <table>
                <tr>
                    <td><div style="text-transform: uppercase; text-weight: bold;">suggestions/comments/commendations</div></td>
                </tr>
                <tr>
                    <td>
                        <div style="font-size: .85em">
                            {{ $list[0]->content }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    </div>
    <div class="footer">
        <div style="text-transform: uppercase; font-size: .65em; font-style: italic;">generated by: eParticipation system | {{ $now }}</div>
    </div>
</body>
</html>