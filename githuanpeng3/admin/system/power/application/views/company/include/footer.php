<!-- Footer starts -->
<footer>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
            <!-- Copyright info -->
            <p class="copy">Copyright &copy; 2017 | <a target="blank" href="http://www.huanpeng.com">欢朋直播</a> </p>
      </div>
    </div>
  </div>
</footer> 	
<!-- Footer ends -->
<!-- Scroll to top -->
<span class="totop"><a href="#"><i class="icon-chevron-up"></i></a></span> 
<!-- JS -->
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/jquery.js"></script> <!-- jQuery -->
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/bootstrap.js"></script> <!-- Bootstrap -->
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/jquery-ui-1.9.2.custom.min.js"></script> <!-- jQuery UI -->
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/fullcalendar.min.js"></script> <!-- Full Google Calendar - Calendar -->
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/jquery.rateit.min.js"></script> <!-- RateIt - Star rating -->
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/jquery.prettyPhoto.js"></script> <!-- prettyPhoto -->

<!-- jQuery Flot -->
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/excanvas.min.js"></script>
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/jquery.flot.js"></script>
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/jquery.flot.resize.js"></script>
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/jquery.flot.pie.js"></script>
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/jquery.flot.stack.js"></script>

<!-- jQuery Notification - Noty -->
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/jquery.noty.js"></script> <!-- jQuery Notify -->
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/themes/default.js"></script> <!-- jQuery Notify -->
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/layouts/bottom.js"></script> <!-- jQuery Notify -->
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/layouts/topRight.js"></script> <!-- jQuery Notify -->
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/layouts/top.js"></script> <!-- jQuery Notify -->
<!-- jQuery Notification ends -->

<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/sparklines.js"></script> <!-- Sparklines -->
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/jquery.cleditor.min.js"></script> <!-- CLEditor -->
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/bootstrap-datetimepicker.min.js"></script> <!-- Date picker --> <!-- jQuery Uniform -->
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/bootstrap-switch.min.js"></script> <!-- Bootstrap Toggle -->
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/filter.js"></script> <!-- Filter for support page -->
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/custom.js"></script> <!-- Custom codes -->
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/charts.js"></script> <!-- Charts & Graphs -->
<script src="<?php echo $this->config->config['adminuser_css_url']; ?>js/highcharts/highcharts.js"></script> <!-- Charts & Graphs -->

<script>
    $('.btn-danger').bind("click",function(event){
        if(!confirm('确定要删除吗？')) {
            event.preventDefault();
        }
    });
</script>