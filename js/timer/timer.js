
app_timer = function()
{
  this.timer_interval = false;
  
  //open timer panel      
  this.open = function()
  {
    $('.panel-timer').removeClass('hidden')
  }
  
  //close timer and close timer panel
  this.close = function()
  {
    warn_msg = $('.button-timer-close').attr('data-warn-msg');
    
    if(confirm(warn_msg))
    {
      window.onbeforeunload = '';
      
      $('.panel-timer').removeClass('timer-active');
      $('.panel-timer').addClass('hidden')
            
      $('#timer-container').attr('data-seconds',0)
      
      app_timer_render();            
      
      this.prepare_buttons()
      
      clearInterval(this.timer_interval);
      
      //remove timer from database
      $('#timer_report').load($('#timer-container').attr('data-action-url'),{        
        action:'delete_timer',
        entities_id: $('#timer-container').attr('data-entities-id'),
        items_id: $('#timer-container').attr('data-items-id')      
      })
      
    }      
  }
  
  //prepare timer action buttons
  this.prepare_buttons = function()
  {
    //display Pause field if timer active
    if($('.panel-timer').hasClass('timer-active'))
    {
      $('.button-timer-start').addClass('hidden')
      $('.button-timer-pause').removeClass('hidden')
      $('.button-timer-continue').addClass('hidden')
      $('.button-timer-close').addClass('hidden')
      $('.button-timer-reset').addClass('hidden')
      
    }
    //display Continue button if timer open
    else if(parseInt($('#timer-container').attr('data-seconds'))>0)
    {
      $('.button-timer-start').addClass('hidden')
      $('.button-timer-pause').addClass('hidden')
      $('.button-timer-continue').removeClass('hidden')
      $('.button-timer-close').removeClass('hidden')
      $('.button-timer-reset').removeClass('hidden')
    }
    //display Start button
    else
    {
      $('.button-timer-start').removeClass('hidden')
      $('.button-timer-pause').addClass('hidden')
      $('.button-timer-continue').addClass('hidden')
      $('.button-timer-close').removeClass('hidden')
      $('.button-timer-reset').removeClass('hidden')
    }
  }
  
  //start timer
  this.start  = function()
  {
    //protect user accidentally leave the page
    window.onbeforeunload = function () { return $('.panel-timer').attr('data-active-timer-msg') };
        
    $('.panel-timer').addClass('timer-active');
    
    this.prepare_buttons()
    
    //render time every seconds
    this.timer_interval = setInterval(function(){
      //get current seconds
      seconds = parseInt($('#timer-container').attr('data-seconds'))+1;
                  
      $('#timer-container').attr('data-seconds',seconds)
      
      app_timer_render();
                                                  
    },1000)
  } 
  
  //timer pause
  this.pause = function()
  {
    window.onbeforeunload = ''; 
      
    $('.panel-timer').removeClass('timer-active');
    
    clearInterval(this.timer_interval);
    
    this.prepare_buttons()
  }
  
  //create timer in database
  this.create = function()
  {
    $('#timer_report').load($('#timer-container').attr('data-action-url'),{        
        action:'create_timer',
        entities_id: $('#timer-container').attr('data-entities-id'),
        items_id: $('#timer-container').attr('data-items-id')      
      })
  }
  
  this.reset = function()
  {
    warn_msg = $('.button-timer-reset').attr('data-warn-msg');
    
    if(confirm(warn_msg))
    {
      $('#timer-container').attr('data-seconds',0)
      
      app_timer_render();
      
      $.ajax({type: "POST",url: $('#timer-container').attr('data-action-url'),data: {
          seconds:0,
          action:'set_timer',
          entities_id: $('#timer-container').attr('data-entities-id'),
          items_id: $('#timer-container').attr('data-items-id')
          }});
    }
  } 
}

//render timer html values
function app_timer_render()
{
  //get seconds
  seconds = parseInt($('#timer-container').attr('data-seconds'));
  
  if(seconds>0)
  {
    if(seconds/60==Math.floor(seconds/60))
    {
      $.ajax({type: "POST",url: $('#timer-container').attr('data-action-url'),data: {
        seconds:seconds,
        action:'set_timer',
        entities_id: $('#timer-container').attr('data-entities-id'),
        items_id: $('#timer-container').attr('data-items-id')
        }});
    }
  }
  
  //get minutes and hours
  minutes = Math.floor(seconds/60);    
  hours = Math.floor(seconds/3600);
            
  //correct seconds          
  if(minutes>0)
  {
    seconds = seconds-(minutes*60);
  }
  
  if(hours>0)
  {
    minutes = minutes-(hours*60);
  }
  
  //calcualte spent hours            
  spent_hours = (hours+(1/(60/minutes)));  
  $('#timer-hours-container').val(spent_hours.toFixed(2));
  
  //prepare output
  if(seconds<10)
  { 
    seconds = '0'+seconds;
  }  
  
  if(minutes<10)
  {
    minutes = '0'+minutes;
  }
  
  if(hours<10)
  {
    hours = '0'+hours;
  }
  
  //update container
  $('#timer-container').html(hours+':'+minutes+':'+seconds);
}

$(function(){

  var timer = new app_timer();
  
  timer.prepare_buttons();
  
  app_timer_render()
  
  $('.button-timer-open').click(function(){
     timer.open()
  })
  
  $('.button-timer-close').click(function(){
    timer.close()        
  })
  
  $('.button-timer-start').click(function(){     
     timer.create()     
     timer.start()
  })
  
  $('.button-timer-continue').click(function(){
     timer.start()
  })
  
  $('.button-timer-pause').click(function(){
     timer.pause()
  })
  
  $('.button-timer-reset').click(function(){
     timer.reset()
  })
  
  //select and copy hours
  $('#timer-hours-container').focus(function() {
        this.select();
        document.execCommand("copy");
    }).mouseup(function() {
        return false;
    });
})