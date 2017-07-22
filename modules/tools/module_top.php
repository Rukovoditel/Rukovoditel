<?php
  //check access
  if($app_user['group_id']>0)
  {
    redirect_to('dashboard/access_forbidden');
  }