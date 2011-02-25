CURR_PAGE = 1;
$(document).ready(function(){
    function fetch_page(page_num) {
        $.ajax({
            url: "/images_fragment.php",
            data: "page=" + page_num,
            cache: false,
            success: function(msg){
                $(image_bank).html(msg);

                // Fade-out preselected ones
                for(var i in images_to_post) {
                    if(images_to_post.hasOwnProperty(i)) {
                        $('#image-' + i).addClass('in-tray');
                    }
                }
            }
        });

    }

    var images_to_post = {};
    var images_in_tray = []; // this is solely for ordering
    var images_to_post_length = 0;

    var image_bank = document.getElementById('image-bank');
    var tray = null;
    var active_image = null;
    var active_image_id = 0;

    if(image_bank) {
        fetch_page(1);
        tray = $('#tray');
        active_image = $('#active-image');
    }

    $('.prev').live('click', function(){
        $(image_bank).html("Loading images...");
        CURR_PAGE--;
        fetch_page(CURR_PAGE);
        return false;
    });

    $('.next').live('click', function(){ 
        $(image_bank).html("Loading images...");
        CURR_PAGE++;
        fetch_page(CURR_PAGE);
        return false;
    });

    function make_image_active(image_id) {
        if(!image_id) return;

        active_image_id = image_id;
        perm = images_to_post[image_id].perm;
        $('input[name="perm"]').filter('[value="' + perm + '"]').click();

        $('#tray img').removeClass('active');
        $('#trayimage-' + active_image_id).addClass('active');
    }

    function enable_form() {
        $('input[name="perm"]').removeAttr('disabled');
        $('#remove').css('visibility', 'visible');
    }

    function disable_form() {
        $('input[name="perm"]').attr('disabled', 'disabled');
        $('#remove').css('visibility', 'hidden');
    }
    disable_form();

    lefts = [5, 89, 173, 257, 341];
    midlines = [42.5, 126.5, 210.5, 294.5, 378.5];

    $('#remove').click(function(){
        delete images_to_post[active_image_id];

        images_to_post_length--;
        if(images_to_post_length == 0) {
            disable_form();
        }
        images_in_tray.splice(images_in_tray.indexOf(active_image_id), 1);

        $('#trayimage-' + active_image_id).remove();
        $('#image-' + active_image_id).removeClass('in-tray');
        
        // Visually refresh tray
        counter = 0;
        $('#tray img').each(function(){ 
            $(this).css('left', lefts[counter] + 'px');
            counter++;
        });
        refresh_draggables();

        id = 0;
        for(var i in images_to_post) {
            id = i;    
        }
        make_image_active(id);

        return false;
    });

    function refresh_draggables() {
        curr_index = null;
        images = [];
        $('#tray img').each(function(){
            images.push($(this));
        }).draggable({
            axis: 'x',
            start: function() {
                curr_index = images_in_tray.indexOf(this.id.replace(/^trayimage-/, ''));
            },
            drag: function() {
                curr_x = $(this).position().left;
                // Check to the left
                if(curr_index > 0 && curr_x < midlines[curr_index - 1]) {
                    images[curr_index - 1].css('left', lefts[curr_index]  + 'px');

                    // Update tray data
                    temp = images_in_tray[curr_index - 1];
                    images_in_tray[curr_index - 1] = images_in_tray[curr_index];
                    images_in_tray[curr_index] = temp;

                    temp_image = images[curr_index - 1];
                    images[curr_index - 1] = images[curr_index];
                    images[curr_index] = temp_image;

                    curr_index = curr_index - 1;
                }
                // Now check to the right
                else if((curr_index < (images.length - 1)) && (curr_x + 75) > midlines[curr_index + 1]) {
                    images[curr_index + 1].css('left', lefts[curr_index]  + 'px');

                    // Update tray data
                    temp = images_in_tray[curr_index + 1];
                    images_in_tray[curr_index + 1] = images_in_tray[curr_index];
                    images_in_tray[curr_index] = temp;

                    temp_image = images[curr_index + 1];
                    images[curr_index + 1] = images[curr_index];
                    images[curr_index] = temp_image;

                    curr_index = curr_index + 1;
                }

            },
            stop: function() {
                images[curr_index].css('left', lefts[curr_index] + 'px');
            },
        });
    }

    $('#tray img').live('click', function() {
        var photo_id = this.id.replace(/^trayimage-/, '');
        make_image_active(photo_id);
        return false;
    });

    $('input[name="perm"]').change(function(){
        images_to_post[active_image_id].perm = $(this).val();
    });

    $('.imagebank-image').live('click', function(){ 
        var photo_id = this.id.replace(/^image-/, '');
        if(images_to_post_length < 5 && !(photo_id in images_to_post)) {
            image_src = $(this).attr('src');
            $(this).addClass('in-tray');
            tray.append('<img style="left:' + lefts[images_to_post_length] + 'px;" id="trayimage-' + photo_id + '" src="' + image_src + '"/>');
            refresh_draggables();

            images_to_post[photo_id] = {perm: 'pb', src: image_src.replace(/_s.jpg$/, '_m.jpg')}; 
            images_in_tray.push(photo_id);
            enable_form();
            make_image_active(photo_id);
            images_to_post_length++;
        }
        return false;
    });

    $('#trickle-it').click(function(){

        if(images_in_tray.length == 0) {
            return false;
        }

        photo_string = '';
        count = 0;
        $(this).text('Trickling...');

        // Form in reverse order (so tray matches up with what shows up in Flickr timeline)
        // photo_ids=1234,1235,1236
        // perms=ff,fr,pb
        images_in_tray.reverse();

        ids = [];
        perms = [];
        for(i in images_in_tray) {
            id = images_in_tray[i];
            ids.push(id);
            perms.push(images_to_post[id].perm);
        }

        var data = {
            ids: ids.join(','),
            perms: perms.join(','),
        };

        $.ajax({
            type: 'POST',
            url: "/trickle.php",
            data: data,
            cache: false,
            success: function(){ 
                window.location = 'http://' + window.location.hostname + '/?success=1'
            }
        });
        return false;
    });

});
