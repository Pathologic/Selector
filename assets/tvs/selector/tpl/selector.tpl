<input style="display:none;" id="tv[+tv_id+]" name="tv[+tv_id+]" value="[+tv_value+]">
<select id="tokenizetv[+tv_id+]" multiple="multiple" class="tokenizetv[+tv_id+]">
    [+values+]
</select>
<script type="text/javascript">
(function($){
    $('#tokenizetv[+tv_id+]').tokenize({
        datas:'[+site_url+]assets/tvs/selector/ajax.php',
        searchParam: 'doc_id=[+doc_id+]&doc_parent=[+doc_parent+]&doc_template=[+doc_template+]&tvid=[+tv_id+]&tvname=[+tv_name+]&ts=[+timestamp+]&search',
        valueField: '[+valueField+]',
        textField: '[+textField+]',
        htmlField: '[+htmlField+]',
        debounce: 600,
        newElements: false,
        nbDropdownElements: [+nbDropdownElements+],
        maxElements: [+maxElements+],
        searchMaxLength: [+searchMaxLength+],
        searchMinLength: [+searchMinLength+],
        displayDropdownOnFocus:true,
        onAddToken: function (value, text, e) {
            selector.update(e.select,$('#tv[+tv_id+]'),'[+divider+]');
        },
        onRemoveToken: function(value, e) {
            selector.update(e.select,$('#tv[+tv_id+]'),'[+divider+]');
        }
    });
    var el = $('.tokenizetv[+tv_id+] ul.TokensContainer');
    var sortabletv[+tv_id+] = new Sortable(el[0], {
        draggable: '.Token',
        filter: '.TokenSearch',
        onMove: function(e) {
            if (e.related.className == "TokenSearch") return false;
        },
        onSort: function (e) {
            selector.sort($('.tokenizetv[+tv_id+]'),$('#tv[+tv_id+]'),'[+divider+]');
        }
    });
})(jQuery);
</script>
