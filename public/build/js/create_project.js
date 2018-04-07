function saveData(url) {
    var obj = {};
    obj.perks = {};

    obj.title = CKEDITOR.instances['title'].getData();
    obj.shortDescription = CKEDITOR.instances['shortDescription'].getData();
    obj.totalAmount = $('#totalAmount').val();
    obj.content = $('#content-area').keditor('getContent');
    obj.presentationMedia = $('#youtube').keditor('getContent');

    $('.perk-title').each(
        function() {
            var perk_id = $(this).attr('data-perk-id');
            obj.perks[perk_id] = {};

            obj.perks[perk_id].title = CKEDITOR.instances[$(this).attr('id')].getData();
        }
    );

    $('.perk-description').each(
        function() {
            var perk_id = $(this).attr('data-perk-id');
            obj.perks[perk_id].description = CKEDITOR.instances[$(this).attr('id')].getData();
        }
    );

    $('.perk-amount').each(
        function() {
            var perk_id = $(this).attr('data-perk-id');
            obj.perks[perk_id].amount = $(this).val();
        }
    );

    $('.perk-image').each(
        function() {
            var perk_id = $(this).attr('data-perk-id');
            obj.perks[perk_id].image_path = $(this).keditor('getContent');
        }
    );

    var jsonString = JSON.stringify(obj);
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: jsonString,
        success: function (result) {
            $('#edit_container').css("background-color", "yellowgreen");
            lastModified = new Date();
            $('#last_save').text(timeSince(lastModified));
        },
        error: function (result) {
            $('#edit_container').css("background-color", "red");
            console.log(result);
        }
    });
}

function fixNumerics() {
    jQuery('<div class="quantity-nav"><div class="quantity-button quantity-up">+</div><div class="quantity-button quantity-down">-</div></div>').insertAfter('.quantity input');
    jQuery('.quantity').each(function () {
        var spinner = jQuery(this),
            input = spinner.find('input[type="number"]'),
            btnUp = spinner.find('.quantity-up'),
            btnDown = spinner.find('.quantity-down'),
            min = input.attr('min'),
            max = input.attr('max');

        btnUp.click(function () {
            var oldValue = parseFloat(input.val());
            if (oldValue >= max) {
                var newVal = oldValue;
            } else {
                var newVal = oldValue + 1;
            }
            spinner.find("input").val(newVal);
            spinner.find("input").trigger("change");
        });

        btnDown.click(function () {
            var oldValue = parseFloat(input.val());
            if (oldValue <= min) {
                var newVal = oldValue;
            } else {
                var newVal = oldValue - 1;
            }
            spinner.find("input").val(newVal);
            spinner.find("input").trigger("change");
        });

        $(this).removeClass('quantity');
        $(this).addClass('quantity-enabled');
    });
}

var keditor = $('#content-area').keditor({
    snippetsUrl: '../../../../build/keditor/snippets/default/snippets.html',
    nestedContainerEnabled: false
});

setTimeout(function () {
    keditor.toggleSidebar();
}, 2000);


/**
 * Enable ckeditor on all elements in page that have the ckeditor-enabled class
 */
CKEDITOR.disableAutoInline = true;
CKEDITOR.inline('title');
CKEDITOR.inline('shortDescription');
enableCKEDITOR('.ckeditor-enabled');


function enableCKEDITOR(className) {
    var elements = CKEDITOR.document.find(className),
        i = 0,
        element;

    while ((element = elements.getItem(i++))) {
        CKEDITOR.inline(element);
    }
}

/**
 * Enable flyckity slider
 */
var elem = document.querySelector('#inner-perks');
var flkty = new Flickity(elem, {
    // options
    cellAlign: 'left',
    contain: true,
    autoPlay: true,
    draggable: false
});

function fixFlyckity(className)
{
    flkty.unbindDrag();
    flkty.bindDrag();

    var DOMs = $(className);

    DOMs.on('mouseenter', function () {
        flkty.unbindDrag();
    });
    DOMs.on('mouseleave', function () {
        flkty.bindDrag();
    });
    DOMs.on('focusout', function () {
        flkty.bindDrag();
    });
}

function timeSince(date) {
    var seconds = Math.floor((new Date() - date) / 1000);

    return Math.floor(seconds);
}
var lastModified = new Date();


$(document).ready(function () {
    fixNumerics();

    flkty.unbindDrag();
    flkty.bindDrag();

    var ckeditorEnabledDOMs = $('.ckeditor-enabled');
    var buttonComponentSettingDOMs = $('.btn-component-setting');
    var closeButtonDOMs = $('.close');

    ckeditorEnabledDOMs.on('mouseenter', function () {
        flkty.unbindDrag();
    });
    ckeditorEnabledDOMs.on('mouseleave', function () {
        flkty.bindDrag();
    });
    ckeditorEnabledDOMs.on('focusout', function () {
        flkty.bindDrag();
    });

    buttonComponentSettingDOMs.on('mouseenter', function () {
        flkty.unbindDrag();
    });
    buttonComponentSettingDOMs.on('mouseleave', function () {
        flkty.bindDrag();
    });
    buttonComponentSettingDOMs.on('focusout', function () {
        flkty.bindDrag();
    });

    closeButtonDOMs.on('mouseenter', function () {
        flkty.unbindDrag();
    });
    closeButtonDOMs.on('mouseleave', function () {
        flkty.bindDrag();
    });
    closeButtonDOMs.on('focusout', function () {
        flkty.bindDrag();
    });

    console.log(timeSince(new Date(Date.now()-lastModified)));
});