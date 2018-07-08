<link href="template/css/pages/pricing-tables.css" rel="stylesheet" type="text/css"/>

<div class="row margin-bottom-40">
	<!-- Pricing -->
	<div class="col-md-3">
		<div class="pricing hover-effect">
			<div class="pricing-head">
				<h3><?php echo TEXT_HEADING_EXTENSION ?>
				<span>
					 <?php echo TEXT_NEW_FEATURES_FOR_YOUR_BUSINESS ?>
				</span>
				</h3>
				<h4>
				<?php echo ($app_user['language']=='russian.php' ? '<a href="http://rukovoditel.net/ru/extension.html" target="_blank"><img src="images/rukovoditel_ext_box_ru.png"></a>':'<a href="http://rukovoditel.net/extension.html" target="_blank"><img src="images/rukovoditel_ext_box_en.png"></a>') ?>				
				</h4>
			</div>
			<ul class="pricing-content list-unstyled">
				<li>
					<i class="fa fa-thumbs-o-up"></i> <?php echo TEXT_ONE_OFF_CHARGE ?>
				</li>
				<li>
					<i class="fa fa-heart"></i> <?php echo TEXT_UPDATES_FOR_FREE?>
				</li>
				<li>
					<i class="fa fa-smile-o"></i> <?php echo TEXT_FREE_SUPPORT?>
				</li>				
			</ul>
			<div class="pricing-footer">
				<p style="padding: 7px 0;">
					 <?php echo sprintf(TEXT_EXTENSION_LICENSE_KEY_IFNO,str_replace('www.','',$_SERVER['HTTP_HOST'])) ?>
				</p>
				
				<?php echo '<a target="_balnk" href="https://www.rukovoditel.net/' . ($app_user['language']=='russian.php' ? 'ru/':''). 'shopping_cart.php" class="btn btn-success">' . TEXT_BUY_EXTENSION . '</a>'?>				
			</div>
		</div>
	</div>
	
	<div class="col-md-4">
	
	<div class="portlet">
						<div class="portlet-title">
							<div class="caption">
								<b><?php echo TEXT_EXTENSION_FEATURES ?></b>
							</div>
							
						</div>
						<div class="portlet-body" style="display: block;">
							<p><?php echo TEXT_EXTENSION_FEATURES_INFO ?></p>
							
							<ul style="list-style:none; padding-left: 0;">
								<?php 
									foreach(explode(',',TEXT_EXTENSION_FEATURES_LIST) as $v)
									{
										echo '<li><i style="color: #428bca" class="fa fa-check" aria-hidden="true"></i> ' . $v . '</li>';
									}
								?>
							</ul>
							<center><a href="http://rukovoditel.net/<?php echo ($app_user['language']=='russian.php' ? 'ru/':'')?>extension.html" target="_blank" class="btn btn-primary"><?php echo TEXT_MORE_INFO ?></a></center>
						</div>
					</div>
					
	</div>
</div>
