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
    <div style="padding: 25px 25px 30px 25px;">
        <div style="text-align: center;">
            <img src="https://lh3.googleusercontent.com/drive-viewer/AK7aPaBeHvFOSlOFglyH3lzw4iOQSnk_nB-nIjbl3idZzRgzDAT1HIZwvkzNSVQkrvy8V-nuND4XC_Ka6zTQlJhvqdaCGdzYFQ=s1600" width="75" height="75" style="border-bottom: 15px;" />
            <div class="h4">Kiosk Report</div>
        </div>
        <div style="padding: 25px 0px 0px 0px;">
            <table>
                @foreach($kiosks as $dt)
                <tr><td width="25%"><div style="text-transform: uppercase; text-weight: bold;">office</div></td>
                    <td width="75%"><div style="font-size: .85em; text-transform: capitalize;">{{ $dt['office'] }}</div></td>
                </tr>
                <tr>
                    <td width="25%"><div style="text-transform: uppercase; text-weight: bold;">complainant</div></td>
                    <td width="75%"><div style="font-size: .85em; text-transform: capitalize;">{{ $dt['name'] }}</div></td>
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
                    <td width="75%"><div style="font-size: .85em;">{{ $dt->date }}</div></td>
                </tr>
                @endforeach
            </table>
        </div>
        <div style="padding: 25px 0px 0px 0px;">
            <table>
                <tr>
                    <td colspan="6"><div style="text-transform: uppercase; text-weight: bold;">satisfaction rating</div></td>
                </tr>
                <tr>
                    <td width="25%"><div style="text-transform: uppercase; text-weight: bold;">-</div></td>
                    <td width="15%"><div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: .80em; ">very satisfied</div></td>
                    <td width="15%"><div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: .80em; ">satisfied</div></td>
                    <td width="15%"><div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: .80em; ">neutral</div></td>
                    <td width="15%"><div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: .80em; ">dissatisfied</div></td>
                    <td width="15%"><div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: .80em; ">poor</div></td>
                </tr>
                <tr>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold;">
                            <div style='font-size: .65em;'>office environment/atmosphere</div>
                        </div>
                    </td>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->phyRating ==5)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->phyRating ==4)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->phyRating ==3)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->phyRating ==2)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->phyRating ==1)
                                &times;
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold;">
                            <div style='font-size: .65em;'>treatment of employee towards you</div>
                        </div>
                    </td>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->serRating ==5)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->serRating ==4)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->serRating ==3)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->serRating ==2)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->serRating ==1)
                                &times;
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold;">
                            <div style='font-size: .65em;'>promptness of requests as for the services we provide</div>
                        </div>
                    </td>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->perRating ==5)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->perRating ==4)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->perRating ==3)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->perRating ==2)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->perRating ==1)
                                &times;
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold;">
                            <div style='font-size: .65em;'>overall experience</div>
                        </div>
                    </td>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->ovrRating ==5)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->ovrRating ==4)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->ovrRating ==3)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->ovrRating ==2)
                                &times;
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="text-transform: uppercase; text-weight: bold; text-align: center; font-size: 2em;">
                            @if ($list[0]->ovrRating ==1)
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
        <div style="text-transform: uppercase; font-size: .5em; font-style: italic;">generated by:</div>
        <div style="text-transform: uppercase; font-size: .5em; font-style: italic;">{{ $name }}</div>
        <div style="text-transform: uppercase; font-size: .5em; font-style: italic;">{{ $now }}</div>
        <div style="text-transform: uppercase; font-size: .5em; font-style: italic; right: 0; top: 0; position: absolute;">eParticipation System</div>
    </div>
</body>
</html>