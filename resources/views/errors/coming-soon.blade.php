<!DOCTYPE html>
<html lang="en">
<head>
	<title>Coming Soon 6</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" type="image/x-icon" href="{{ asset('uploads/photos/'.config('settings.favicon')) }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-social.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/waves.min.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/comingsoon/css/flipclock.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/comingsoon/css/style.css') }}">
</head>
<body>
	<div class="wrapper">
		<header>
			<div class="container-fluid">
				<div class="row justify-content-between">
					<div class="col-auto"><a href="#"><img src="{{ asset('themes/comingsoon/images/logo.png') }}" alt="LOGO"></a></div>
					<div class="col-auto">
						<a href="#" class="btn btn-social-icon btn-facebook">
							<i class="fa fa-facebook"></i>
						</a>

						<a href="#" class="btn btn-social-icon btn-twitter">
							<i class="fa fa-twitter"></i>
						</a>

						<a href="#" class="btn btn-social-icon btn-google">
							<i class="fa fa-youtube-play"></i>
						</a>
					</div>
				</div>
			</div>
		</header>
		<main>
			<div class="container-fluid">
				<div class="row justify-content-center">
					<div class="col-auto">
						<p class="text-white text-out-website">
							Our website is
						</p>

						<h3 class="text-white text-coming-soon">
							Coming Soon
						</h3>

						<div class="countdowntime"></div>
					</div>
					<div class="col-auto d-none">
						<form action="#" method="post">
						<h5>{{ __('Newsletter') }}</h5>
					    <div class="form-group">
					    	<input type="text" value="" name="name" class="form-control form-control-lg" placeholder="Name">
					    </div>
					    <div class="form-group">
					    	<input type="email" value="" name="email" class="form-control form-control-lg" placeholder="Email">
					    </div>
					    <div class="form-group">
					    	<button type="submit" class="btn btn-success btn-block btn-lg btn-ajax text-uppercase" data-ajax="act=newsletter|type=newsletter"> {{ __('Đăng ký') }} </button>
					    </div>
					    <p class="text-secondary">
							Chúng tôi luôn cố gắng đem lại cho bạn những ưu đãi tốt nhất.
						</p>
					</form>
					</div>
				</div>
			</div>
		</main>
	</div>
	<script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/jquery-migrate.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/waves.min.js') }}"></script>
	<script src="{{ asset('themes/comingsoon/js/flipclock.min.js') }}"></script>
	<script src="{{ asset('themes/comingsoon/js/moment.js') }}"></script>
	<script src="{{ asset('themes/comingsoon/js/moment-timezone.js') }}"></script>
	<script src="{{ asset('themes/comingsoon/js/moment-timezone-with-data.js') }}"></script>
	<script type="text/javascript">
		(function ($) {
		    "use strict";

		    $.fn.extend({ 

		      countdown: function(options) {
		        var defaults = {
		          timeZone: "",
		          endtimeYear: 0,
		          endtimeMonth: 0,
		          endtimeDate: 0,
		          endtimeHours: 0,
		          endtimeMinutes: 0,
		          endtimeSeconds: 0,
		        }

		        var options =  $.extend(defaults, options);

		        return this.each(function() {
		          var obj = $(this);
		          var timeNow = new Date();

		          var tZ = options.timeZone;
		          var endYear = options.endtimeYear;
		          var endMonth = options.endtimeMonth;
		          var endDate = options.endtimeDate;
		          var endHours = options.endtimeHours;
		          var endMinutes = options.endtimeMinutes;
		          var endSeconds = options.endtimeSeconds;

		          if(tZ == "") {
		            var deadline = new Date(endYear, endMonth - 1, endDate, endHours, endMinutes, endSeconds);
		          } 
		          else {
		            var deadline = moment.tz([endYear, endMonth - 1, endDate, endHours, endMinutes, endSeconds], tZ).format();
		          }

		          if(Date.parse(deadline) < Date.parse(timeNow)) {
		            var deadline = new Date(Date.parse(new Date()) + endDate * 24 * 60 * 60 * 1000 + endHours * 60 * 60 * 1000); 
		          }
		          
		          var t = Date.parse(deadline) - Date.parse(new Date());
		            
		          var clock = $(obj).FlipClock(t/1000, {
		            clockFace: 'DailyCounter',
		            countdown: true
		          });


		        });
		      }
		    });
		    $('.countdowntime').countdown({
				/*Set Endtime here*/
				/*Endtime must be > current time*/
				endtimeYear: 0,
				endtimeMonth: 0,
				endtimeDate: 25,
				endtimeHours: 18,
				endtimeMinutes: 0,
				endtimeSeconds: 0,
				timeZone: ""
			});
		    Waves.init({duration: 500, delay: 200});
        	Waves.attach('.btn', ['waves-light']);
		})(jQuery);
	</script>
</body>
</html>