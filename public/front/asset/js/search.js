
$('[name="model_hotkeyss"]')
    .focus(function () {

        me = $(this);


        $(this).keyup(function () {
            if (me.val() != '') {

                //	me.parents( '.formTheme' ).append( clearText );

                clearTextYes = true;
            } else {
                $('.clear-text').remove();
                clearTextYes = false;
            }

            me.autocomplete({
                source: me.attr('data-link'),
                select: function (event, ui) {

                    window.location = ui.item.link;

                    return false;
                }

            });
        });
    });

