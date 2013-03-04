<?php
/*
 * This file is part of the sfLucenePlugin package
 * (c) 2007 Carl Vondrick <carlv@carlsoft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(SF_ROOT_DIR . '/plugins/sfLucenePlugin/modules/sfLucene/lib/BasesfLuceneActions.class.php');

/**
 * @package    sfLucenePlugin
 * @subpackage Module
 * @author     Carl Vondrick <carlv@carlsoft.net>
 */
class sfLuceneActions extends BasesfLuceneActions
{
  public function executeSearch()
  {
    if (CmsConfiguration::get('check_use_layout_per_section', false))
    {
      VirtualSection::setCurrentId(VirtualSection::VS_SEARCH);
    }

    $this->getRequest()->getParameterHolder()->set('section_name', 'search_section');

    $this->fields = array_merge(array(
      'all'      => false,
      'articles' => false,
      'titles'   => false,
      'uploads'  => false
    ), $this->getRequestParameter('fields', array('all' => true)));

    $query = $this->getRequestParameter('query', null);

    if ($this->getRequest()->getMethod() == sfRequest::POST || $query !== null)
    {

      $search_query = SearchQueryBuilder::build($query, $this->fields);

      $this->pager = $this->getResults($search_query);

      $this->configurePager($this->pager);

      if ($this->getRequestParameter('google', false))
      {
        // If a Google search was selected, redirect to Google
        $google = sprintf('http://www.google.com.ar/search?ie=UTF-8&oe=UTF-8&domains=%s&sitesearch=%s&q=%s',
          $this->getRequest()->getHost(),
          $this->getRequest()->getHost(),
          urlencode($query)
        );
        
        $this->redirect($google);
      }
      else
      {
        // Perform a local search
        $this->results = $this->pager->getNbResults();
        $this->query   = $query;
      }
    }
    else
    {
      // No search has been performed yet
      $this->query   = null;
      $this->results = false;
    }
  }

  /**
   * Get the results of issuing $query string to Lucene.
   *
   * @param  SearchQuery $querystring
   * @param  mixed       $category
   *
   * @return sfLucenePager
   */
  protected function getResults($querystring, $category = null)
  {
    $this->getLogger()->log('{ sfLucene } Building query: '.strval($querystring));

    $query = $querystring->asLuceneCriteria($this->getLuceneInstance());
    
    $this->getLogger()->log('{ sfLucene } Resulting query: '.strval($query->getQuery()));

    return new sfLucenePager($this->getLuceneInstance()->friendlyFind($query));
  }

}
