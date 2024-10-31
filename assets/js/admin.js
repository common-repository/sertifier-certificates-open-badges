var ajax_url = plugin_ajax_object.ajax_url;

jQuery(document).ready(function ($) {
    var selectedUserIds = [];
    addUsers();
    getLessons();
    jQuery("select[name=course_id]").on("change", function () {
        addUsers();
        getLessons();
    });

    jQuery("input#search").keyup(
        debounce(function () {
            addUsers(true);
        }, 500)
    );

    jQuery("select[name=delivery_id]").change(function () {
        addUsers();
    });

    jQuery("select#search_course").change(function () {
        addUsers(true);
    });

    async function addUsers(isSearch = false) {
        var getUserData = {
            action: "get_users",
            course_id: jQuery("select#search_course").val(),
            query: jQuery("input#search").val(),
            delivery_id: jQuery("select[name=delivery_id]").val(),
        };

        await $.ajax({
            url: ajax_url,
            type: "post",
            data: getUserData,
            dataType: "json",
            success: function (response) {
                if (!isSearch) selectedUserIds = response.selectedUserIds;

                $("#result").html("");
                $.each(response.users, function (i, item) {
                    $("#result").append(
                        `<label class="user-item">
                            <input name="users[]" type="checkbox" 
                            ` +
                            (selectedUserIds.includes(item.ID)
                                ? "checked"
                                : "") +
                            ` value="` +
                            item.ID +
                            `"> ` +
                            item.display_name +
                            ` (` +
                            item.user_email +
                            `)
                        </label>`
                    );
                });
                jQuery("span#users-helper").html(
                    selectedUserIds.length == 0
                        ? "No student selected"
                        : selectedUserIds.length +
                              (selectedUserIds.length == 1
                                  ? " student"
                                  : " students") +
                              " selected"
                );
            },
        });

        jQuery("input[name='users[]']").change(function () {
            if (this.checked) {
                selectedUserIds.push(this.value);
            } else {
                let tempValue = this.value;
                selectedUserIds = jQuery.grep(
                    selectedUserIds,
                    function (value) {
                        return value != tempValue;
                    }
                );
            }
            jQuery("input[name=usersNew]").val(selectedUserIds);
            jQuery("span#users-helper").html(
                selectedUserIds.length == 0
                    ? "No student selected"
                    : selectedUserIds.length +
                          (selectedUserIds.length == 1
                              ? " student"
                              : " students") +
                          " selected"
            );
        });
    }

    function getLessons() {
        if ($("select[name=lesson_id]").length == 0) {
            return;
        }

        var getLessonData = {
            action: "get_lessons",
            course_id: jQuery("select[name=course_id]").val(),
        };

        $.ajax({
            url: ajax_url,
            type: "post",
            data: getLessonData,
            dataType: "json",
            success: function (response) {
                $("select[name=lesson_id]").html("");
                $("select[name=lesson_id]").append(
                    $("<option>", {
                        value: 0,
                        text: "Please select a lesson (Optional)",
                    })
                );
                $.each(response, function (i, item) {
                    $("select[name=lesson_id]").append(
                        $("<option>", {
                            value: item.id,
                            text: item.post_title,
                        })
                    );
                });
            },
        });
    }

    function debounce(func, wait, immediate) {
        var timeout;
        return function () {
            var context = this,
                args = arguments;
            var later = function () {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }
});

function deleteRecord(id, e, action = "delete_auto_issue") {
    var deleteRecordData = {
        action: action,
        id: id,
    };

    jQuery.ajax({
        url: ajax_url,
        type: "post",
        data: deleteRecordData,
        dataType: "json",
        success: function () {
            jQuery(e).closest("tr").remove();
        },
    });
}
