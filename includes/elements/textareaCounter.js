"use strict";
$(() => {
    $(".anchor-textarea").on("change",(event) => {
        const value = $(event.target).val();
        const length = value.length;

        if(length <= 255) return $(event.target).parent().find(".anchor-characterCount").html(length);

        const trimmed = $(event.target).val(value.substring(0, 255));
    });
    
    $(".anchor-textarea").on("keydown",(event) => {
        $(event.target).change();
    });
});