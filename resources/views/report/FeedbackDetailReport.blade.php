<!DOCTYPE html>
<html lang="en">
<head>
    <title>Feedback Detail</title>
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
            background-color: red;
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
            <div class="h4">Feedback Detail</div>
        </div>
        <div style="padding: 25px 0px 0px 0px;">
            <table>
                <tr>
                    <td width="25%"><div style="text-transform: uppercase; text-weight: bold;">complainant</div></td>
                    <td width="75%"><div style="font-size: .85em; text-transform: capitalize;">{{ $feedbacks[0]->name }}</div></td>
                </tr>
                <tr>
                    <td width="25%"><div style="text-transform: uppercase; text-weight: bold;">email</div></td>
                    <td width="75%"><div style="font-size: .85em;">{{ $feedbacks[0]->email }}</div></td>
                </tr>
                <tr>
                    <td colspan='2' style='padding-top: 5px; padding-bottom: 5px;'><div style="text-transform: uppercase; text-weight: bold;">feedback details</div></td>
                </tr>
                <tr>
                    <td width="25%"><div style="text-transform: uppercase; text-weight: bold;">status</div></td>
                    <td width="75%">
                        <div style="font-size: .85em;">
                        @if ($feedbacks[0]->status == 1)
                        PENDING
                        @endif
                        @if ($feedbacks[0]->status == 2)
                        ONGOING
                        @endif
                        @if ($feedbacks[0]->status == 3)
                        COMPLETED
                        @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="25%"><div style="text-transform: uppercase; text-weight: bold;">category</div></td>
                    <td width="75%"><div style="font-size: .85em;">{{ $feedbacks[0]->category }}</div></td>
                </tr>
                <tr>
                    <td width="25%"><div style="text-transform: uppercase; text-weight: bold;">overall rating</div></td>
                    <td width="75%"><div style="font-size: .85em;">{{ $rating }}%</div></td>
                </tr>
                <tr>
                    <td colspan='2'>
                        <div style="">
                        {{ $feedbacks[0]->content }}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan='2' style='padding-top: 5px; padding-bottom: 5px;'><div style="text-transform: uppercase; text-weight: bold;">feedback responses</div></td>
                </tr>
                @foreach ($responses as $dt)
                <tr>
                    <td colspan='2'>
                        <div style="text-transform: uppercase; text-weight: bold;">
                        {{ $dt['name'] }}
                        </div>
                        <div style="text-transform: capitalize; font-size: .85em;">
                        {{ $dt['office'] }}
                        </div>
                        <div style="font-size: .65em;">
                        {{ $dt['content'] }}
                        </div>
                    </td>
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