function saveData(url) {
    var obj = {};
    obj.perks = {};
    // for (var key in CKEDITOR.instances) {
    //     if (CKEDITOR.instances.hasOwnProperty(key)) {
    //         obj[key] = CKEDITOR.instances[key].getData();
    //     }
    // }

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

        },
        error: function (result) {
            console.log(result)
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

var elements = CKEDITOR.document.find('.ckeditor-enabled'),
    i = 0,
    element;

while ((element = elements.getItem(i++))) {
    CKEDITOR.inline(element);
}

$(document).ready(function () {
    fixNumerics();

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

    flkty.unbindDrag();
    flkty.bindDrag();

    $('.ckeditor-enabled').on('mouseenter', function () {
        flkty.unbindDrag();
    });
    $('.ckeditor-enabled').on('mouseleave', function () {
        flkty.bindDrag();
    });
    $('.ckeditor-enabled').on('focusout', function () {
        flkty.bindDrag();
    });

    $('.btn-component-setting').on('mouseenter', function () {
        flkty.unbindDrag();
    });
    $('.btn-component-setting').on('mouseleave', function () {
        flkty.bindDrag();
    });
    $('.btn-component-setting').on('focusout', function () {
        flkty.bindDrag();
    });

    $('.close').on('mouseenter', function () {
        flkty.unbindDrag();
    });
    $('.close').on('mouseleave', function () {
        flkty.bindDrag();
    });
    $('.close').on('focusout', function () {
        flkty.bindDrag();
    });
});