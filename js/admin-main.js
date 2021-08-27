jQuery(document).ready(function ($) {

    $("#snillrik_calendar_addeventdate").on("click", function () {
        let datum = $("#snillrik_calendar_dateadd").val();
        let tid = $("#snillrik_calendar_timeadd").val();
        //alert("watt"+datum+" "+tid);
        //let fulllist = "";
        let fulllist = [];

        $('#snillrik_calendar_times_list tr').each(function () {
            let datum_td = $(this).find(".snillrik_event_listdate").html();
            let time_td = $(this).find(".snillrik_event_listtime").html();
            if (datum_td)
                fulllist.push({ "datum": datum_td, "tid": time_td });
        });

        fulllist.push({ "datum": datum, "tid": tid });

        $('#snillrik_calendar_timeslist_full').val(JSON.stringify(fulllist)); //store array
        //$("#snillrik_calendar_timeslist_full").append("<tr><td>"+datum+"</td><td>"+tid+"</td><td>Tabort</td></tr>");
        $("#snillrik_calendar_times_list").append("<tr><td class='snillrik_event_listdate'>" + datum + "</td><td class='snillrik_event_listtime'>" + tid + "</td><td><span class='delete_box'>X</span></td></tr>");
        //add in pretty list.


    });

    $("#snillrik_calendar_times_list").on("click", ".delete_box", function (e) {
        let parent = $(this).parent().parent();

        if (confirm("Vill du verkligen plocka bort det datumet?")){
            parent.remove();
            let fulllist = [];

            $('#snillrik_calendar_times_list tr').each(function () {
                let datum_td = $(this).find(".snillrik_event_listdate").html();
                let time_td = $(this).find(".snillrik_event_listtime").html();
                if (datum_td)
                    fulllist.push({ "datum": datum_td, "tid": time_td });
            });

    
            $('#snillrik_calendar_timeslist_full').val(JSON.stringify(fulllist)); //store array

        }

    });
});