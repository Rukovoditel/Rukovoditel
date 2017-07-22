<?php

  class split_page {
    var $sql_query, $number_of_rows, $current_page_number, $number_of_pages, $number_of_rows_per_page, $page_name, $listing_container;

/* class constructor */
    function __construct($query,$listing_container,$count_sql_query = 'query_num_rows',$rows_per_page = 0) {
      global $_GET, $_POST;

      $this->listing_container = $listing_container;
      
      
      $this->sql_query = $query;
      $page_holder = 'page';
      $this->page_name = $page_holder;

      if (isset($_GET[$page_holder])) {
        $page = $_GET[$page_holder];
      } elseif (isset($_POST[$page_holder])) {
        $page = $_POST[$page_holder];
      } else {
        $page = '';
      }

      if (empty($page) || !is_numeric($page)) $page = 1;
      $this->current_page_number = $page;
			      
      $this->number_of_rows_per_page  = ($rows_per_page>0 ? $rows_per_page : CFG_APP_ROWS_PER_PAGE);
      
      
      if(strlen($count_sql_query)>0)
      {
      	if($count_sql_query=='query_num_rows')
      	{
      		$count_query = db_query($this->sql_query);
      		$count['total'] = db_num_rows($count_query);      		
      	}	
      	else
      	{	
        	$count_query = db_query($count_sql_query);
        	$count = db_fetch_array($count_query);
      	}
      }
      else
      {       
        $pos_to = strlen($this->sql_query);
        $pos_from = stripos($this->sql_query, ' from', 0);
  
        $count_query = db_query("select count(*) as total " . substr($this->sql_query, $pos_from, ($pos_to - $pos_from)));
        $count = db_fetch_array($count_query);
      }

      $this->number_of_rows = $count['total'];

      $this->number_of_pages = ceil($this->number_of_rows / $this->number_of_rows_per_page);

      if ($this->current_page_number > $this->number_of_pages) {
        $this->current_page_number = $this->number_of_pages;
      }

      $offset = ($this->number_of_rows_per_page * ($this->current_page_number - 1));

      $this->sql_query .= " limit " . max($offset, 0) . ", " . $this->number_of_rows_per_page;      
    }

/* class functions */

// display split-page-number-links
    function display_links($parameters = '') {
      global $PHP_SELF, $request_type;
      
      if($this->number_of_pages==1 or $this->number_of_rows==0)
      {
        return '';
      }

      $max_page_links = 10;
      $html = '<div class="dataTables_paginate paging_bootstrap"><ul class="pagination">';
      
      if (strlen($parameters)>0 && (substr($parameters, -1) != '&')) $parameters .= '&';

// previous button - not displayed on first page
      if ($this->current_page_number > 1)
      {
        $html .= '<li><a href="#" onClick="load_items_listing(\'' . $this->listing_container . '\',' . ($this->current_page_number - 1) . '); return false;"  title=" ' . PREVNEXT_TITLE_PREVIOUS_PAGE . ' "><i class="fa fa-angle-left"></i></a></li>';
      }
      else
      {
        $html .= '<li class="active"><a href="#" onClick="return false"><i class="fa fa-angle-left"></i></a></li>';
      }

// check if number_of_pages > $max_page_links
      $cur_window_num = intval($this->current_page_number / $max_page_links);
      if ($this->current_page_number % $max_page_links) $cur_window_num++;

      $max_window_num = intval($this->number_of_pages / $max_page_links);
      if ($this->number_of_pages % $max_page_links) $max_window_num++;

// previous window of pages
      if ($cur_window_num > 1) $html .= '<li><a href="#" onClick="load_items_listing(\'' . $this->listing_container . '\',' . (($cur_window_num - 1) * $max_page_links) . '); return false;" title=" ' . sprintf(PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE, $max_page_links) . ' ">...</a></li>';

// page nn button
      for ($jump_to_page = 1 + (($cur_window_num - 1) * $max_page_links); ($jump_to_page <= ($cur_window_num * $max_page_links)) && ($jump_to_page <= $this->number_of_pages); $jump_to_page++) 
      {
        if ($jump_to_page == $this->current_page_number) 
        {
          $html .= '<li class="active"><a href="#"  onClick="return false">' . $jump_to_page . '</a></li>';
        } 
        else 
        {
          $html .= '<li><a href="#" onClick="load_items_listing(\'' . $this->listing_container . '\',' . $jump_to_page . '); return false;" title=" ' . sprintf(PREVNEXT_TITLE_PAGE_NO, $jump_to_page) . ' ">' . $jump_to_page . '</a></li>';
        }
      }

// next window of pages
      if ($cur_window_num < $max_window_num) $html .= '<li><a href="#"  onClick="load_items_listing(\'' . $this->listing_container . '\',' . ($cur_window_num * $max_page_links + 1) . ')" title=" ' . sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links) . ' ">...</a></li>';

// next button
      if (($this->current_page_number < $this->number_of_pages) && ($this->number_of_pages != 1))
      { 
        $html .= '<li><a href="#"  onClick="load_items_listing(\'' . $this->listing_container . '\',' . ($this->current_page_number + 1) . '); return false;" title=" ' . PREVNEXT_TITLE_NEXT_PAGE . ' "><i class="fa fa-angle-right"></i></a></li>';
      }
      else
      {
        $html .= '<li class="active"><a href="#"  onClick="return false"><i class="fa fa-angle-right"></i></a></li>';
      }
      
      $html .= '</ul></div>';

      return $html;
    }

// display number of total products found
    function display_count() {
      $to_num = ($this->number_of_rows_per_page * $this->current_page_number);
      if ($to_num > $this->number_of_rows) $to_num = $this->number_of_rows;

      $from_num = ($this->number_of_rows_per_page * ($this->current_page_number - 1));

      if ($to_num == 0) {
        $from_num = 0;
      } else {
        $from_num++;
      }

      return sprintf(TEXT_DISPLAY_NUMBER_OF_ITEMS, $from_num, $to_num, $this->number_of_rows);
    }
  }