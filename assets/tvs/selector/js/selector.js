/**
 * Created by Pathologic on 13.06.2015.
 */
(function($){
    selector = {
        update: function(container, target) {
            var tvvalue = [];
            $('option:selected', container).each(function(){
                tvvalue.push($(this).attr('value'));
            });
            target.val(tvvalue.join(','));
        },
        sort: function(container, target) {
            select = container.first();
            select.empty();
            $('ul.TokensContainer li.Token',container).each(function() {
                    var value = $(this).data('value');
                    var text = $('span',this).html();
                    var option = $('<option />')
                        .attr('selected', 'selected')
                        .attr('value', value)
                        .html(text);
                    select.append(option);

                }
            );
            this.update(select,target);
        }
    }
    $('body').attr('ondragstart','');
})(jQuery)