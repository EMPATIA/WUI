<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>{{ trans("privateUsers.export_title") }}</title>
        <style>
            h1, h2, h3, h4, h5, h6 {
                color: #006b72;
            }
        </style>
    </head>
    <body style="font-size:50%;font-family:'DejaVu Sans',Sans-Serif;overflow-x:hidden;">
        <div>
            <div>
                <div style="background-color:#7EAC37;height:15px;"></div>
                <div style="text-align:left;max-height:150px;width:100%;">
                    <img src="data:image/png;base64,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"  style="float: right; margin-top:20px"/>
                </div>
            </div>
        </div>

        <section class="content">
            <h1 style="margin-top:30px;margin-bottom:0;color:#006b72;">{{ trans("privateUsers.export_title") }}</h1>
            <h4 style="margin-top:0;margin-bottom:0;color:#006b72;">
                {{ trans("privateUsers.export_users_count") }}: {{ count($usersArray) }}
            </h4>
            <h4 style="margin-top:0;margin-bottom:40px;color:#006b72;">
                {{ trans("privateUsers.export_date") }}: {{ \Carbon\Carbon::now()->format("Y-m-d H:i:s") }}
            </h4>

            @foreach($usersArray as $user)
                <div>
                    <table style="width:100%;">
                        <tr>
                            <td style="width:50%;">
                                <h2 style="color:#006b72;">
                                    {{ trans("privateUsers.name") }}:
                                    {{ $user["name"] }}
                                </h2>
                            </td>
                            <td style="width:50%;text-align:right;">
                                {{ $user["role"] }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width:50%;">
                                <b>{{ trans("privateUsers.email") }}:</b>
                                {{ $user["email"] }}
                            </td>
                            <td style="width:50%;">
                                <b>{{ trans("privateUsers.created_at") }}:</b>
                                {{date('Y-m-d h:i:s', strtotime($user["created_at"])) }}
                            </td>
                        </tr>
                        <tr><td><br></td></tr>
                        @foreach ($user["parameters"] as $userParameter)
                            @if (($loop->iteration%2)==1)
                                <tr>
                            @endif
                                    <td style="width:50%;">
                                        <b>{{ $userParameter["name"] }}:</b>
                                        {{ $userParameter["value"] }}
                                    </td>
                            @if (($loop->iteration%2)==0 || $loop->last)
                                </tr>
                            @endif
                        @endforeach
                    </table>
                </div>
                <hr style="margin:15px 0;">
            @endforeach
        </section>
    </body>
</html>