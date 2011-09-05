<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* @package ExpressionEngine
* @author Wouter Vervloet
* @copyright Copyright (c) 2011, Baseworks
* @license http://creativecommons.org/licenses/by-sa/3.0/
* 
* This work is licensed under the Creative Commons Attribution-Share Alike 3.0 Unported.
* To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/
* or send a letter to Creative Commons, 171 Second Street, Suite 300,
* San Francisco, California, 94105, USA.
*/

require PATH_THIRD."outline/config.php";

$plugin_info = array(
  'pi_name' => OUTLINE_NAME,
  'pi_version' => OUTLINE_VERSION,
  'pi_author' => 'Wouter Vervloet',
  'pi_author_url' => 'http://www.baseworks.nl/',
  'pi_description' => OUTLINE_DESCRIPTION,
  'pi_usage' => Outline::usage()
);


class Outline {

	var $return_data = '';
	
	var $site_id = 1;
	
	var $tree = array();
  
	public function Outline()
	{
	  $this->__construct();
	}
	
	public function __construct()
	{
		$this->EE =& get_instance(); 	  
		
		$this->site_id = $this->EE->config->item('site_id');
	}
	

  public function nav()
  {
    
    // if ( ! isset($this->EE->session->userdata[__CLASS__]['tree']))
    // {
      
      $include_ul = $this->_fetch_bool_param('include_ul', TRUE);
      $include = $this->_fetch_array_param('include');
      
      $site_pages = $this->EE->config->item('site_pages');
      $pages = isset($site_pages[$this->site_id]['uris']) ? $site_pages[$this->EE->config->item('site_id')]['uris'] : array();
    
      $entry_ids = array_keys($pages);
    
      $entries = $this->EE->db->select('entry_id, title, url_title, status')->where_in('entry_id', $entry_ids)->where('site_id', $this->site_id)->get('channel_titles');
    
      $nodes = array();
    
      if ($entries->num_rows() > 0)
      {
        foreach($entries->result_array() as $row)
        {
          $row['page_url'] = $pages[$row['entry_id']];
          $row['children'] = array();
          $nodes[$row['entry_id']] = $row;
        }
      
      }
    
      $this->_build_tree($nodes);
      
      $this->EE->session->userdata[__CLASS__]['tree'] = $this->tree;
    // }
    // else
    // {
    //   $this->tree = $this->EE->session->userdata[__CLASS__]['tree'];    
    // }
    
    foreach ($this->tree as $key => $val)
    {
      if ( ! in_array($val['url_title'], $include)) 
      {
        unset($this->tree[$key]);
      }
    }
        
    $html = $this->_render_tree($this->tree, $include_ul);
        
    return $html;
    
  }
  
  private function _build_tree($nodes=array())
  {
    foreach ($nodes as $entry_id => $node)
    {
      $segments = explode('/', trim($node['page_url'], '/'));
      $pos = '$this->tree';
      
      foreach($segments as $i => $seg)
      {
        if (isset($segments[$i+1]))
        {
          $pos .= "['$seg']['children']";
        }
        else
        {
          $pos .= "['$seg']";
        }
      }
      
      $pos .= ' = $node;';
  
      eval($pos);
      
    }
  }
  
  private function _render_tree($tree=array(), $include_ul=TRUE)
  {
    $str = ($include_ul) ? "<ul>" : "";
    
    foreach ($tree as $node)
    {
      $node_active = FALSE;
      $parent_active = FALSE;
      
      if ( ! isset($node['page_url'])) continue;
      
      if ($this->EE->uri->uri_string == trim(@$node['page_url'], '/'))
      {
        $node_active = TRUE;
      }
      else if (strpos($this->EE->uri->uri_string, trim(@$node['page_url'], '/')) !== FALSE)
      {
        $parent_active = TRUE;
      }
      
      $str .= '<li';
      
      if ($node_active === TRUE)
      {
        $str .= ' class="active"';
      }
      else if ($parent_active === TRUE)
      {
        $str .= ' class="parent_active"';        
      }
            
      $str .= '><a href="'.@$node['page_url'].'">'.@$node['title'].'</a>';
      
      if ( (isset($node['children']) AND count($node['children']) > 0) AND ($node_active OR $parent_active) )   
      {
        $str .= $this->_render_tree($node['children']);
      }
      
      $str .= "</li>";
    }
    
    $str .= ($include_ul) ? "</ul>" : "";
    
    return $str;
  }
  
  
	
  /**
  * Helper function for getting a parameter
  */		 
  private function _fetch_param($key='', $default_value = FALSE)
  {
    $val = $this->EE->TMPL->fetch_param($key);

    if ($val === '' OR $val === FALSE)
    {
      return $default_value;
    }
    
    return $val;
  }	
  
  private function _fetch_bool_param($key='', $default_value = FALSE)
  {
    $val = $this->_fetch_param($key, $default_value);
    
    return in_array($val, array('y', 'yes', '1', 'true')) ? TRUE : FALSE;
  }

  private function _fetch_array_param($key='', $default_value = FALSE)
  {
    $val = $this->_fetch_param($key, $default_value);
    
    return strpos($val, '|') !== FALSE ? explode('|', $val) : (array) $val;
  }

  public function usage()
  {
	  ob_start(); 
  ?>

  

  <?php
	  $buffer = ob_get_contents();

	  ob_end_clean(); 

	  return $buffer;
	  }
	  // END

	}

/* End of file pi.outline.php */ 
/* Location: ./system/expressionengine/third_party/plugin_name/pi.outline.php */