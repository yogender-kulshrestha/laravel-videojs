@extends('layouts.app')
@section('style')
<meta http-equiv="refresh" content="30" />
<style>
.showTrancoding {
    position: relative;
    background-color: red;
    text-align: center;
}

.showTrancoding img {
    opacity: 0.5;
}

.showLoading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    width: 100%;
}
</style>
@endsection
@section('content')
<div class="container shadow-sm">
    <div class="row bg-dark rounded-top">
        <div class="col-6 col-md-4 order-1 order-sm-1 p-2 text-white">
            <div class="mt-2">
                List of videos
            </div>
        </div>
        <div class="col-12 col-md-6 order-3 order-sm-2 p-2 text-end ">
            <div class="input-group">
                <input class="form-control border-end-0 border rounded-pill" type="search" value="" placeholder="Search"
                    aria-label="Search" id="search-input">
                <span class="input-group-append">
                    <button class="btn btn-outline-secondary bg-white border-bottom-0 border rounded-pill ms-n5"
                        type="button">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        </div>
        <div class="col-6 col-md-2 order-2 order-sm-3 p-2 text-end">
            <a class="btn btn-primary btn-sm mt-1" href="{{ route('video.upload') }}"> <i class="fas fa-plus"></i>
                Create</a>
        </div>
    </div>
    <div class="row border-bottom">
        <div class="col-2 col-md-1 p-3">
            <div class="d-flex align-items-center">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="selectAll">
                </div>
                <strong>ID</strong>
            </div>

        </div>
        <div class="col-md-3 d-none d-sm-block">&nbsp;</div>
        <div class="col-10 col-md-8 p-3">
            <div class="d-flex align-items-center">
                <strong class="d-none d-sm-block">Title</strong>
                <div class="form-check">
                    <button class="btn btn-sm btn-danger mb-0" data-bs-toggle="modal"
                        data-bs-target="#deleteSelectedVideos" id="deleteSelected">
                        <i class="fas fa-trash"></i>
                        Delete selected
                    </button>
                </div>
            </div>

        </div>
    </div>
    <div id="videosLists">
        @include('video.videoListSearchData')
    </div>
    <div class="text-center py-4" id="searchStart">
        <div class="spinner-grow text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow text-secondary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow text-success" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow text-danger" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow text-warning" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow text-info" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    <!-- Vertically centered modal delete selected videos -->
    <div class="modal fade" id="deleteSelectedVideos" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Video</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the selected videos?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-danger" onclick="deleteSelected()">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ asset('js/Sortable.min.js') }}"></script>
<script>
$('#searchStart').hide();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


$('#search-input').keyup(function() {
    $('#searchStart').show();
    $('#videosLists').hide();
});
$('#search-input').keyup(debounce(function() {
    var value = $(this).val();
    value = value.trim()
    if (value.length > 0) {
        getSeachData(value);
    } else {
        getSeachData('all');
    }

}, 1000));


function debounce(func, wait, immediate) {
    var timeout;
    return function() {
        var context = this,
            args = arguments;
        var later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
};

function getSeachData(value, page = 1) {
    $.ajax({
        url: '/video/get-search?page=' + page + '&search=' + value,
        type: 'Get',
        data: null,
        success: function(data) {
            $('#videosLists').html(data);
            setTimeout(() => {
                $('#searchStart').hide();
                $('#videosLists').show();
            }, 500);

        }
    });
}

$(function() {
    $(document).on("click", "#pagination a", function(e) {
        e.preventDefault();
        $('#searchStart').show();
        $('#videosLists').hide();
        var page = $(this).attr('href').split('page=')[1];
        var value = $('#search-input').val();
        getSeachData(value, page);
    })
});

function deleteVideo(slug) {
    $.ajax({
        url: '/video/delete/' + slug,
        type: 'Post',
        success: function(result) {
            if (result.success == 'true') {
                window.location.href = "{{route('video.index')}}";
            }

        }
    })
}


$('#selectAll').change(function() {
    // check all checkboxes
    if ($(this).prop('checked')) {
        $('.checkItem').prop('checked', true);
    } else {
        $('.checkItem').prop('checked', false);
    }
    // if checked any checkbox show delete button
    if ($('.checkItem:checked').length > 0) {
        $('#deleteSelected').show();
    } else {
        $('#deleteSelected').hide();
    }
});

$(".checkItem").click(function() {
    var check_count = $(".checkItem:checked");
    if (check_count.length > 0) {
        $("#deleteSelected").show();
    } else {
        $("#deleteSelected").hide();
    }
});

// if any of checkbox is not checked then unchecked the select all checkbox
$(".checkItem").click(function() {
    if ($(this).prop("checked") == false) {
        $("#selectAll").prop("checked", false);
    }
});
// if all checkboxes are checked then checked the select all checkbox
$(".checkItem").click(function() {
    if ($(".checkItem:checked").length == $(".checkItem").length) {
        $("#selectAll").prop("checked", true);
    }
});

function deleteSelected() {
    var selected = [];
    $('.checkItem:checked').each(function() {
        selected.push($(this).val());
    });

    $.ajax({
        url: '/video/delete-selected',
        type: 'Post',
        data: {
            deleteSelected: JSON.stringify(selected)
        },
        success: function(result) {
            if (result.success == 'true') {
                Swal.fire({
                    title: 'Delete selected',
                    text: 'Delete all selected videos.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                })
                window.location.href = "{{route('video.index')}}";
            } else {
                Swal.fire({
                    title: 'Error',
                    text: 'Something went wrong',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                })
            }
        }
    })
}


Sortable.create(videosLists, {
    sort: true, // sorting inside list
    delay: 100, // time in milliseconds to define when the sorting should start
    animation: 150,
    dragoverBubble: true,
    ghostClass: 'ghost',
    onChange: function(evt) {
        var item_order = new Array();
        $('#videosLists #individualList').each(function() {
            item_order.push($(this).attr("index"));
        });
        var order_string = 'order=' + item_order;
        $.ajax({
            url: '/video/update-order',
            type: 'Post',
            data: order_string,
            success: function(result) {
                getSeachData('all')
            }
        })
    }

});
</script>
@endsection