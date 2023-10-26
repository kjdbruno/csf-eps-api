<!DOCTYPE html>
<html lang="en">
<head>
    <title>Discussion Detail</title>
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
            <img src="https://www.sanfernandocity.gov.ph/csflu_website/wp-content/uploads/2020/04/San-Fernando-Seal_1-500x500.jpg" width="75" height="75" style="border-bottom: 15px;" />
            <div class="h4">Discussion Thread & Poll</div>
        </div>
        <div style="padding: 25px 0px 0px 0px;">
            <table>
                @foreach($discussions as $dt)
                <tr>
                    <td width="20%"><div style="text-transform: uppercase; text-weight: bold;">category</div></td>
                    <td width="80%"><div style="font-size: .85em; text-transform: capitalize;">{{ $dt['category'] }}</div></td>
                </tr>
                <tr>
                    <td width="20%"><div style="text-transform: uppercase; text-weight: bold;">title</div></td>
                    <td width="80%"><div style="font-size: .85em; text-transform: capitalize;">{{ $dt['title'] }}</div></td>
                </tr>
                <tr>
                    <td width="20%"><div style="text-transform: uppercase; text-weight: bold;">content</div></td>
                    <td width="80%"><div style="font-size: .85em; text-transform: capitalize;">{{ $dt['content'] }}</div></td>
                </tr>
                @endforeach
            </table>
        </div>
        <div style="padding: 25px 0px 0px 0px;">
            <table>
                <tr>
                    <td colspan="2"><div style="text-transform: uppercase; text-weight: bold;">thread</div></td>
                </tr>
                @foreach($threads as $dt)
                <tr>
                    <td width="30%">
                        <div style="text-transform: uppercase; text-weight: bold; font-size: .85em;">{{$dt['firstname']}}&nbsp;{{$dt['lastname']}}</div>
                        <div style="text-transform: uppercase; text-weight: bold; font-size: .5em;">{{$dt['office']}}</div>
                    </td>
                    <td width="70%"><div style="font-size: .85em;">{{ $dt['content'] }}</div></td>
                </tr>
                @endforeach
            </table>
        </div>
        <div style="padding: 25px 0px 0px 0px;">
            <table>
                <tr>
                    <td colspan="2"><div style="text-transform: uppercase; text-weight: bold;">poll</div></td>
                </tr>
                @foreach($poll as $dt)
                <tr>
                    <td width="30%">
                        <div style="text-transform: uppercase; text-weight: bold; font-size: .85em;">{{$dt['label']}}</div>
                    </td>
                    <td width="70%"><div style="font-size: .85em;">{{ $dt['count'] }}</div></td>
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