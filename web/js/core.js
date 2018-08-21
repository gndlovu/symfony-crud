jQuery.extend({
    doAJAX: function (url, data, type, callback)
    {
        if (type.toLowerCase() != "get")
        {
            data["_token"] = _token;
        }
        
        type = !(type) ? "GET" : type;

        loading('start');

        var object = {
            type: type,
            url: url,
            data: data,
            // dataType: "json",
            error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                if(XMLHttpRequest.responseJSON)
                {
                    notify('error', XMLHttpRequest.responseJSON.error.message);
                }
                else
                {
                    notify('error',"Error Code: " + XMLHttpRequest.status + " : " + XMLHttpRequest.statusText);
                }

                loading('stop');
            },
            success: function (data)
            {
                loading('stop');
                callback(data);
            }
        };

        if(type == 'MULTIPART')
        {
            object.type = 'POST';
            object.data = data.params;
            object.contentType = false;
            object.processData = false;
        }

        //Hide all tooltips
        $(".tooltip").tooltip("hide");
        
        return $.ajax(object);

    }
});

function notify(type, msg, title, time_out)
{
    //toastr.clear();

    time_out = (time_out == undefined) ? 0 : time_out;

    toastr.options = {
        "closeButton": true,
        "closeHtml": "<i class='fa fa-remove'></i>",
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": 300,
        "hideDuration": 1000,
        "timeOut": time_out,
        "extendedTimeOut": 0,
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    toastr[type](msg, title);
}


function show_errors(error_description)
{
    if($.type(error_description) == 'object')
    {
        $.each(error_description, function(i,v)
        {
            notify("error", v);
        });
    }
    else
    {
        notify("error", error_description);

        if(error_description == 'Token has expired')
        {
            setTimeout(function()
            {
                window.location.replace(base_url + "/logout");
            }, 5000);
        }
    }
}

function rn()
{
    var d = new Date();
    var h = d.getHours();
    var m = d.getMinutes();
    var s = d.getSeconds();

    if (h < 10) {h="0"+h;}
    if (m < 10) {m ="0"+m;}
    if (s < 10) {s="0"+s;}

    var clk = h + ":" + m + ":" + s;
    $('#footer-server-clock').html("Server Time: " + clk + " CET");
    $('#nav-server-clock').html(clk + " CET");
}

setInterval(rn,500);