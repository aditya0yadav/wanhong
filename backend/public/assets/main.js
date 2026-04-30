(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner();
    
    
    // Initiate the wowjs
    new WOW().init();


    // Sticky Navbar
    $(window).scroll(function () {
        if ($(this).scrollTop() > 30) {
            $('.navbar').addClass('sticky-top shadow-sm');
        } else {
            $('.navbar').removeClass('sticky-top shadow-sm');
        }
    });
    
    // Dropdown on mouse hover
    const $dropdown = $(".dropdown");
    const $dropdownToggle = $(".dropdown-toggle");
    const $dropdownMenu = $(".dropdown-menu");
    const showClass = "show";
    
    $(window).on("load resize", function() {
        if (this.matchMedia("(min-width: 992px)").matches) {
            $dropdown.hover(
            function() {
                const $this = $(this);
                $this.addClass(showClass);
                $this.find($dropdownToggle).attr("aria-expanded", "true");
                $this.find($dropdownMenu).addClass(showClass);
            },
            function() {
                const $this = $(this);
                $this.removeClass(showClass);
                $this.find($dropdownToggle).attr("aria-expanded", "false");
                $this.find($dropdownMenu).removeClass(showClass);
            }
            );
        } else {
            $dropdown.off("mouseenter mouseleave");
        }
    });


    // Facts counter
    $('[data-toggle="counter-up"]').counterUp({
        delay: 10,
        time: 2000
    });
    
    
    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });
	
			/* Send mail Ajax Call */
if ($("#loginForm").length > 0) {
    $('#loginForm').on('submit', function(e) {
		
        if (e.isDefaultPrevented()) {
            // handle the invalid form...
        } else {
            e.preventDefault();
            var myform = $('#loginForm');
            var formdata = $(myform).serialize();
            var ajaxUrl = $(myform).attr('action');
            var buttonHtml = '&nbsp;&nbsp;Please Wait...&nbsp;&nbsp;<i class="fa fa-circle-o-notch fa-spin fa-fw"></i>';
            $('button[type="submit"]').attr('disabled', 'disabled');
            $('button[type="submit"]').html(buttonHtml);
            $.ajax({
                type: 'POST',
                url: ajaxUrl,
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(data) {
                    $('#loginconfirmModal').modal("show");
                    var resp = $.parseJSON(data);
						if(resp.status == 'Success'){
							$('.modal-body').html('<h3 class="text-center text-success">'+resp.msg+'</h3>');
						}else{
							$('.modal-body').html('<h3 class="text-center text-danger">'+resp.msg+'</h3>');
						}
                        $('#loginconfirmModal').modal("show");
					},
                error: function() {
                    $('.modal-body').html('<h3 class="text-center text-danger">There is some error in sending message!!!</h3>');
                }
            });
        }
    });

    /* On Hide modal reload url */
    $('#loginconfirmModal').on('hidden.bs.modal', function(e) {
        location.reload();
    });
}	
	if ($("#contactForm").length > 0) {
    $('#contactForm').on('submit', function(e) {
		
        if (e.isDefaultPrevented()) {
            // handle the invalid form...
        } else {
            e.preventDefault();
            var myform = $('#contactForm');
            var formdata = $(myform).serialize();
            var ajaxUrl = $(myform).attr('action');
            var buttonHtml = '&nbsp;&nbsp;Please Wait...&nbsp;&nbsp;<i class="fa fa-circle-o-notch fa-spin fa-fw"></i>';
            $('button[type="submit"]').attr('disabled', 'disabled');
            $('button[type="submit"]').html(buttonHtml);
            $.ajax({
                type: 'POST',
                url: ajaxUrl,
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(data) {
                    $('#confirmModal').modal("show");
                    var resp = $.parseJSON(data);
						if(resp.status == 'Success'){
							$('.modal-body').html('<h3 class="text-center text-success">'+resp.msg+'</h3>');
						}else{
							$('.modal-body').html('<h3 class="text-center text-success">'+resp.msg+'</h3>');
						}
                        $('#confirmModal').modal("show");
					},
                error: function() {
                    $('.modal-body').html('<h3 class="text-center text-danger">There is some error in sending message!!!</h3>');
                }
            });
        }
    });

    /* On Hide modal reload url */
    $('#confirmModal').on('hidden.bs.modal', function(e) {
        location.reload();
    });
}


    // Testimonials carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1500,
        dots: true,
        loop: true,
        center: true,
        responsive: {
            0:{
                items:1
            },
            576:{
                items:1
            },
            768:{
                items:2
            },
            992:{
                items:3
            }
        }
    });


    // Vendor carousel
    $('.vendor-carousel').owlCarousel({
        loop: true,
        margin: 45,
        dots: false,
        loop: true,
        autoplay: true,
        smartSpeed: 1000,
        responsive: {
            0:{
                items:2
            },
            576:{
                items:4
            },
            768:{
                items:6
            },
            992:{
                items:8
            }
        }
    });
    
})(jQuery);

