(function($){
    function wpie_change_registration_url_by_plan(){
        var chosen_type = $('input[name="wpie_key_type"]').attr('value');
        if('lite' == chosen_type){
            $('#wpie_register_link').attr('href',wpie_options.wpie_lite_registration_url)
        }else{
            $('#wpie_register_link').attr('href',wpie_options.wpie_registration_url)
        }
    }

    $(document).ready(function(){

        wpie_change_registration_url_by_plan();

        $(document)
            .on('click','#plan_type_lite',function(){
                $('#plan_type_normal').removeClass('btn-primary').addClass('btn-success');
                $('#plan_type_lite').removeClass('btn-default').addClass('btn-primary');
                $('input[name="wpie_key_type"]').attr('value','lite');
                wpie_change_registration_url_by_plan();
            })
            .on('click','#plan_type_normal',function(){
                $('#plan_type_lite').removeClass('btn-primary').addClass('btn-default');
                $('#plan_type_normal').removeClass('btn-success').addClass('btn-primary');
                $('input[name="wpie_key_type"]').attr('value','normal');
                wpie_change_registration_url_by_plan();
            })
            .on('click','#wpie_enabled',function(){
                $('#wpie_disabled').removeClass('btn-danger').addClass('btn-default');
                $('#wpie_enabled').removeClass('btn-default').addClass('btn-primary');
                $('input[name="wpie_enabled"]').attr('value','1');
            })
            .on('click','#wpie_disabled',function(){
                $('#wpie_enabled').removeClass('btn-primary').addClass('btn-default');
                $('#wpie_disabled').removeClass('btn-default').addClass('btn-danger');
                $('input[name="wpie_enabled"]').attr('value','0');
            })
            .on('click','#adv',function(){
            $("#advpane").toggle();
        });

    });
})(jQuery);