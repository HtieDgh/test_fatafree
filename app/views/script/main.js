$(document).ready(function(){
    var menu_sw=true;
    
    function dispMenu(){
        if(menu_sw){
            $('.menu_wrap').css('display','flex');
        }else{
            $('.menu_wrap').css('display','none');
        }
        menu_sw=!menu_sw;
    }
    
    function add_rec(){
        
       if($('.main_content').attr('value')=='mainPage.htm' || !$('input[value="Добавить"]')) return true;
       document.location.href=$('#add_rec').attr('href');
    }
    
    $(document).keydown(function(e){
        console.log(e.which);
        if(!$('input').is(':focus')){
            switch(e.which){
                case 66:
                    dispMenu(menu_sw);
                    break;
                case 65:
                    add_rec();
                    break;
                /*case 24:
                    if($('.main_content').attr('value')=='mainPage.htm') break;
                    document.location.href=$('#add_rec').attr('href');
                    break;*/
                case 27:
                    dispMenu(menu_sw);
                    break;
            }
        }
    });
    $('#show_menu').click(function(){
        dispMenu();
    });
    $('.menu_wrap').click(function(){
        dispMenu();
    });
    $('#add_rec_btn').click(function(){
       add_rec(); 
    });
    
    if(sessionStorage['col']!==null){
        let column=$('#'+sessionStorage['col']);
        
        column.addClass('table_head_col_fltr');
        column.attr('href',column.attr('href')+'/d');
        sessionStorage['col']=null;
    }
  
    $('.table_head_col').click(function(e){
        e.preventDefault();
        sessionStorage.setItem('col', $(this).attr('id'));
        document.location.href=$(this).attr('href');
    });
    console.log(sessionStorage);
})