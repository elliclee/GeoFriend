//define global variables
var friendLoaded = false;
var neighbourLoaded = false;
var mineLoaded = false;
var mineEditLoaded = false;
var recommendLoaded = false;

//Total slide count
var slideCount;
var thisSlideSeq;
var nextSlideSeq;
            
//Friend slide count
var friendSlideCount;
var friendThisSlideSeq;
var friendNextSlideSeq;
            
//Recommend slide count
var recommendSlideCount;
var recommendThisSlideSeq;
var recommendNextSlideSeq;
            
//Tag related variables
var tagCount;
var tagId;
var tagMaxNumber;
            
$(document).ready(function(){

    $("#neighbour_content").show();
    $("#slide_control").show();

    if(!neighbourLoaded)loadNeighbour(1);

    $("#friend_content").hide();
    $("#mine_content").hide();
    $("#edit_button").hide();
    $("#cancel_button").hide();
    
    //Modified by Stan 2011/7/4
    $("#prev_button").hide();
                
    $("#main_tab").bind('vclick',function (){
        $("#neighbour_content").show();
        $("#slide_control").show();

        $("#mine_content").hide();
        $("#friend_content").hide();
                    
        if(thisSlideSeq > 1)$("#prev_button").show();
        $("#next_button").show();
                    
        $("#edit_button").hide(); 
        $("search_button").show();
    });
                
    $("#friend_tab").bind('vclick',function (){
        $("#friend_content").show();    
        if(!friendLoaded)loadFriend(1);
        $("#slide_control").hide();

        $("#neighbour_content").hide();
        $("#mine_content").hide();
                    
        $("#prev_button").hide();
        $("#next_button").hide();
                    
        $("#edit_button").hide();
        $("search_button").show();
        $("ul.l-list li.l-friend-more").bind('vclick', getNextFriendSlide);
    });
                
    $("#mine_tab").bind('vclick',function (){
                    
        $("#mine_content").show();
        if(!mineLoaded)loadMine();
        $("#slide_control").hide();

        $("#neighbour_content").hide();
        $("#friend_content").hide();
        
        $("#prev_button").hide();
        $("#next_button").hide();

                    
        $("#edit_button").show();
        $("search_button").hide();
        
        //for new tweet operation in profile.php
        $("#new_tweet").live("mousedown",function (){
            $("#submit_tweet").removeClass("l-displaynone");
            $(this).val("");
        });
        
        $("#submit_tweet").live("vclick", function (){
            //Todo 评论存入数据库 并在回调函数中 执行hideComment
            $.post("tweet_update.php", {
                content:$(this).parent().prev().val()
            },function(){
                alert('状态更新成功');       
            });
        });
    });
    
    $("#recommend_button").bind('vclick', function (){
        $.mobile.changePage('recommend.php', 'slideup');
        if(!recommendLoaded)loadRecommend(1);
        $("ul.l-list li.l-recommend-more").live('vclick', getNextRecommendSlide);
    });
                
    //    $("#search_button").bind('vclick',function(){                
    //        $.mobile.changePage('search.php', 'slideup');
    //    });
                
    $("#refresh_button").bind("vclick", refresh)
    $("#edit_button").bind('vclick',function(){
        loadMineEdit();
        $(this).hide();
    });
    $("#next_button").live('vclick',getNextSlide);
    $('#prev_button').live('vclick',getPrevSlide);

});
         
         
//added 07-15 Stan
function refresh(){
    //Clear content
    $("#neighbour_content div").remove();
    //Reset state variables
    neighbourLoaded = false;
    
    if(!neighbourLoaded)loadNeighbour(1);
    
    
    if(friendLoaded){
        $("#friend_content ul.l-list li.l-list-item").remove();
        friendLoaded = false;
        if(!friendLoaded)loadFriend(1);
    }
    
    if(mineLoaded){
        $("#mine_content ul.l-profile-view li").remove();
        mineLoaded = false;
        if(!mineLoaded)loadMine();
    }
    
}

//newly added 06-15 stan
function getNextSlide(){
    //Get slide count
    slideCount = $('div.l-slide:first').attr("data-slide-count");
    if(thisSlideSeq < Number($('div.l-slide:last').attr('data-slide-seq'))){
        nextSlideSeq = Number(thisSlideSeq) + 1;
        slideLeft();
    }
    else if(!thisSlideSeq){
        thisSlideSeq = $("div.l-slide:last").attr('data-slide-seq');
        nextSlideSeq = Number(thisSlideSeq) + 1;
        if(nextSlideSeq <= slideCount)loadNeighbour(nextSlideSeq);
    }
    
    //Display buttons
    $("#prev_button").show();
    if(nextSlideSeq >= slideCount)$("#next_button").hide();
}
            
function getPrevSlide(){
    //Implemented by Stan 2011-6-27
    nextSlideSeq = Number(thisSlideSeq) - 1;
    if(nextSlideSeq >= 1)slideRight();
    
    //Display buttons
    $("#next_button").show();
    if(thisSlideSeq <= 1)$("#prev_button").hide();
}

//Added by Stan 2011-6-27
function slideLeft(){

    //Step 1 before transition, hide new slide and its pager                 
    //Hide this slide pager before this page appears
    $('.l-slide:last').next().hide();

    //Set this slide to the rightmost
    $('.l-slide:last').css('left','210%');
    
    
    //Step 2 hide last pager and slide last slide to the leftmost                
    //Hide last slide pager
    $('.l-slide:last').prev().hide();

    //Set old slide to the leftmost and hide it
    $('.l-slide:last').prev().prev().animate({
        'left':'-110%',
        'opacity':'100'
    }, 'normal',function (){
        $(this).css('display', 'none');
    });
    
    
    //Step 3 slide new slide to the center and display the pager
    $('.l-slide:last').delay(450).animate({
        'left':'0'
    }, 'normal',function (){
        $(this).css('display', 'block')
        $(this).next().show();
    });
    
    thisSlideSeq++;
    bindSwipe();
}

//Added by Stan 2011-6-27
function slideRight(){

    //Step 1 hide new pager and slide new slide to the rightmost                
    //Hide new slide pager
    $('.l-slide:last').next().hide();
    
    $('.l-slide:last').animate({
        'left':'210%',
        'opacity':'100'
    }, 'normal',function (){
        $(this).css('display', 'none');
    });
    

    //Slide old slide to the center
    $('.l-slide:last').delay(450).show().prev().prev().animate({
        'left':'0'
    }, 'normal',function (){
        $(this).css('display','block');
        $(this).next().show();
    });
    
    //    
    //    //Step 3 slide new slide to the center and display the pager
    //    $('.l-slide:last').delay(450).animate({
    //        'left':'0'
    //    }, 'normal',function (){
    //        $(this).next().show();
    //    });

    thisSlideSeq --;
    bindSwipe();
}

function bindSwipe(){
    $("div.l-slide:last").bind('swipeleft',getNextSlide);
    $("div.l-slide:last").bind('swiperight',getPrevSlide);
}
            
//Function to deal with friend loading
function getNextFriendSlide(){
    friendSlideCount = $('div#friend_content ul.l-list li:first-child').attr("data-slide-count");
    
    friendNextSlideSeq = Number(friendThisSlideSeq) +1;
    if(friendThisSlideSeq < friendSlideCount ){
        loadFriend(friendNextSlideSeq);
        if(friendNextSlideSeq == friendSlideCount)$("ul.l-list li.l-friend-more").hide();
    }
}

//Function to deal with recommend loading
function getNextRecommendSlide(){
    recommendSlideCount = $('div#recommend_content ul.l-list li:first-child').attr("data-slide-count");
    
    recommendNextSlideSeq = Number(recommendThisSlideSeq) +1;
    if(recommendThisSlideSeq < recommendSlideCount ){
        loadRecommend(recommendNextSlideSeq);
        if(recommendNextSlideSeq == recommendSlideCount)$("ul.l-list li.l-recommend-more").hide();
    }
}



//Private functions
function addTag()
{
    $(this).show();
    $('.l-taglist input').each(function (){
        //enhancePage();
        tagId = $(this).attr("data-tag-id");
        if ( tagId == tagCount) {
            $(this).parent().show();
                        
            tagCount++;
            if(tagCount >= tagMaxNumber){
                tagCount--;
                displayMessage("max tag size");
                hideMessage(1000);
                            
            }
            return false;
        }
    });
    $(this).addClass('l-display');
    $(this).addClass('l-button-inactive');
}
            
function checkNumber(){
    //alert($(this).attr('value'));
    var warning = $(this).siblings('label.l-warning');
    warning.addClass('l-hidden');

    var number = Number($(this).attr('value'));
    var max = Number($(this).attr('max'));
    var min = Number($(this).attr('min'));
    if((number > max)||(number < min))warning.removeClass('l-hidden');
}
            
function setImage(fatherElem)
{
    $(fatherElem).find(".l-avatar").each(function (){
        if($(this).attr("data-img") != null)
            $(this).css("background-image","url("+$(this).attr("data-img")+")");
    });
}

            
//A serial of load function
function loadNeighbour(slide_seq){
    displayMessage();
    if (typeof slide_seq == 'undifined')slide_seq = 1;
    url = "neighbour.php?slide_seq=" + slide_seq;
    //var spacing = $('body').width();
    $.get(url, function(data){
        hideMessage();
        $("#neighbour_content").append(data);
        setImage(".l-people");
        
        //Modified by Stan 2011-6-27
        slideLeft();
    });
    neighbourLoaded = true;                
}
            
function loadFriend(slide_seq){
    displayMessage();
    if (typeof slide_seq == 'undifined')slide_seq = 1;
    url = "friend.php?slide_seq=" + slide_seq;
    $.get(url, function(data){
        hideMessage();
        $("div#friend_content ul.l-list li.l-friend-more").before(data);
        $("div#friend_content ul.l-list").listview('refresh');
        setImage(".l-list");
    });
    friendLoaded = true;
    
    friendThisSlideSeq = slide_seq;
}

function loadRecommend(slide_seq){
    displayMessage();
    if (typeof slide_seq == 'undifined')slide_seq = 1;
    url = "recommend_people.php?slide_seq=" + slide_seq;
    $.get(url, function(data){
        hideMessage();
        $("ul.l-list li.l-recommend-more").before(data);
        $("ul.l-list").listview('refresh');
        setImage(".l-list");
    });
    recommendLoaded = true;
    
    recommendThisSlideSeq = slide_seq;
}
            
function loadMine(){
    displayMessage();
    $("ul.l-profile-view").load("profile.php ul.l-profile-view li", function(){
        hideMessage();
        $("ul.l-profile-view").listview('refresh');
        setImage(".l-profile-view li");
    });
    mineLoaded = true;                
}  
            
function loadMineEdit(){
    $.mobile.changePage('profile_edit.php', 'slideup');
    $('div').live('pageshow',function(){
        //Slide 1px up to adjust view
        $.mobile.silentScroll (1);
                    
        //Bind number checker onto keypress event
        $('input.l-number').keyup(checkNumber);
                    
        tagCount = $('#new_tag').attr('data-tag-count');
        tagMaxNumber = $('#new_tag').attr('data-tag-max-number');

        $('.l-taglist div').each(function (){
            var tagId = Number($(this).children('input').attr('data-tag-id'));
            if(tagId >= tagCount)$(this).hide();
        });
        // late bind click event 
        $('.l-taglist div:last-child input').next('a').bind('tap',addTag);
    //$('#new_tag + a').bind('tap',addTag);
                    
    // $('a#edit_confirm').bind('vclick', postForm);
    //$('a#edit_confirm').bind('tap', postForm);
    });
}
            
function displayMessage(message){
    $("<div class='l-message ui-loader ui-body-a ui-corner-all'>" + "<span class='ui-icon ui-icon-loading spin'></span>" + "<h1>" + (message?message:"loading") + "</h1>" + "</div>")
    .css({
        "display": "block", 
        "opacity": 0.96, 
        "top": $.support.scrollTop && $(window).scrollTop() + $(window).height() / 2 || 100
    })
    .appendTo( $.mobile.pageContainer );
}
            
function hideMessage(time){
    $(".l-message").fadeOut( time?time:400, function(){
        $(this).remove();
    });
}