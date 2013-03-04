<script type="text/javascript">
//<![CDATA[

//--------------------------------------------------------------------------//


/*
 * http://code.google.com/apis/ajaxsearch/documentation/reference.html#_intro_GResult
 * 
 * Draws results according to their class
 * Returns a string ready to be printed
 * 
 */

function GoogleSearchResultDrawer()
{

    this.getHtlmRepresentation = function(aGResult)
    {
        this.draw(aGResult)
    }
    this.draw = function(aGResult)
    {
        result = ""
            switch (aGResult.GsearchResultClass)
            {
                case "GwebSearch":
                    result += "<p class='result'>"
                    result += "<span class='title'><a href='"+aGResult.url+"'>"+aGResult.title+"</a></span><br>"
                    result += "<span class='description'>"+aGResult.content+"</span><br>"
                    result += "<span class='url'>"+aGResult.url+"</span><br>"
                    result += "</p>"
                    break
                    case "GlocalSearch":
                    result += "<p class='result'>"
                    result += "<span class='title'><a href='"+aGResult.url+"'>"+aGResult.title+"</a></span><br>"
                    result += "<span class='description'>Lat.: "+aGResult.lat+" Long.: "+aGResult.lng+"</span><br>"
                    result += "<span class='url'>"+aGResult.url+"</span><br>"
                    result += "</p>"
                    break
                    case "GvideoSearch":
                    result += "<p class='result'>"
                    result += "<span class='title'><a href='"+aGResult.url+"'>"+aGResult.title+" ("+aGResult.duration+")</a></span><br>"
                    result += "<span class='description'>"+aGResult.content+"</span><br>"
                    result += "<span class='description'><img src='"+aGResult.tbUrl+"' width='"+aGResult.width+"' height='"+aGResult.height+"'></span><br>"
                    result += "<span class='url'>"+aGResult.url+"</span><br>"
                    result += "</p>"
                    break
                    case "GblogSearch":
                    result += "<p class='result'>"
                    result += "<span class='title'><a href='"+aGResult.url+"'>"+aGResult.title+" ("+aGResult.publishedDate+")</a></span><br>"
                    result += "<span class='description'>"+aGResult.content+"</span><br>"
                    result += "<span class='url'>"+aGResult.url+"</span><br>"
                    result += "</p>"
                    break
                    case "GnewsSearch":
                    result += "<p class='result'>"
                    result += "<span class='title'><a href='"+aGResult.url+"'>("+aGResult.location+" - "+aGResult.publishedDate+")"+aGResult.title+"</a></span><br>"
                    result += "<span class='description'>"+aGResult.content+"</span><br>"
                    result += "<span class='url'>"+aGResult.url+"</span><br>"
                    result += "</p>"
                    break
                    case "GbookSearch":
                    result += "<p class='result'>"
                    result += "<span class='title'><a href='"+aGResult.url+"'>"+aGResult.title+"</a></span><br>"
                    result += "<span class='description'>"+aGResult.content+"<br>"+aGResult.authors+" ("+aGResult.pageCount+")</span><br>"
                    result += "<span class='url'>"+aGResult.url+"</span><br>"
                    result += "</p>"
                    break
                    case "GimageSearch":
                    result += "<p class='result'>"
                    result += "<span class='title'><a href='"+aGResult.url+"'>"+aGResult.title+"</a></span><br>"
                    result += "<span class='description'>"+aGResult.content+"</span><br>"
                    result += "<span class='description'><img src='"+aGResult.tbUrl+"' width='"+aGResult.tbWidth+"' height='"+aGResult.tbHeight+"'><br>"+aGResult.width+"x"+aGResult.height+"</span><br>"
                    result += "<span class='url'>"+aGResult.url+"</span><br>"
                    result += "<span class='url'>"+aGResult.originalContextUrl+"</span><br>"
                    result += "</p>"
                    break
                default:
                    result += "ERROR"
                        break;
            }
        return result
    }
}



function GoogleSearcher(){

    searchers = null;
    searcherRestrictions = null;
    searchResults = null;
    videoFlag = false; // Used to signal if a searcher is a video or not (look below)

        this.WEB_SEARCH = 0
        this.IMAGE_SEARCH = 1
        this.VIDEO_SEARCH = 2
        this.BLOG_SEARCH = 3
        this.NEWS_SEARCH = 4
        this.BOOK_SEARCH = 5
        this.LOCAL_SEARCH = 6
        this.IMAGE_AND_VIDEO = 7

        /*
           Arguments:
           - A search type (WEB_SEARCH, etc.)
           - A restriction for the searcher ("info.unlp.edu.ar", "unlp.edu.ar", etc.). May be null
           */
        this.addSearcher = function(anInt, aString)
        {
            searchers = new Array();
            searcherRestrictions = aString;
            searcher = null;
            switch (anInt)
            {
                case this.WEB_SEARCH:
                    searcher = new GwebSearch();
                    break;
                case this.IMAGE_SEARCH:
                    searcher = new GimageSearch();
                    break;
                case this.VIDEO_SEARCH:
                    searcher = new GvideoSearch();
                    videoFlag = true;
                    break;
                case this.BLOG_SEARCH:
                    searcher = new GblogSearch();
                    break;
                case this.NEWS_SEARCH:
                    searcher = new GnewsSearch();
                    break;
                case this.BOOK_SEARCH:
                    searcher = new GbookSearch();
                    break;
                case this.LOCAL_SEARCH:
                    searcher = new GlocalSearch();
                    break;
                case this.IMAGE_AND_VIDEO:
                    searcher = new GimageSearch();
                    //searchers.push(new GvideoSearch());
                    break;
                default:
                    break;
            }

            // There are options that do not work well with video searches, so we ask...
            if (!videoFlag){
                if (searcherRestrictions != null)
                    searcher.setSiteRestriction(searcherRestrictions)
                        searcher.setResultSetSize(GSearch.LARGE_RESULTSET)
            }
            videoFlag = false;
            searcher.setSearchCompleteCallback(null, onSearch); // onSearch is defined below
            searchers.push(searcher);
        }

    // Bug: no tiene en cuenta image_and_video
    this.removeSearcher = function(i)
    {
        if (searchers[i] != null){
            searchers = searchers.concat(searchers.slice(0,i), searchers.slice(i))
                searchersType = searchersType.concat(searchersType.slice(0,i), searchersType.slice(i))
                searchersRestrictions = searchersRestrictions.concat(searchersRestrictions.slice(0,i), searchersRestrictions.slice(i))
        }
    }

    this.getSearchRestriction = function()
    {
        return searcherRestrictions;
    }

    // The html element (e.g., a DIV) where the results will be printed
    this.setSearchResults = function(aString)
    {
        if (document.getElementById(aString))
            searchResults = document.getElementById(aString);
        else
            alert("No existe el elemento, GIL!!!!") // Medio trucho....
    }

    /*
     * Executes and displays the results
     *
     * Arguments:
     *               - aString, that is the term to search for
     *
     * Returns:
     *               - null
     *
     * Side effects:
     *               - Draws/prints the search results in the html element passed as argument to setSearchResults. If the element does not exist, it prints an alert()
     */ 
    this.execute = function(aString)
    {
        if (searchResults == null)
        {
            alert("ERROR: El search result es nulo")
                return
        }
        for (i=0; i<searchers.length; i++)
            searchers[i].execute(aString)
    }

    // This function is called when a search ends. It is a hook
    function onSearch()
    {
        searchResults.innerHTML = ""
            printer = new GoogleSearchResultDrawer()

            for (i=0; i<searchers.length; i++)
            {
                if (!searchers[i].results) break;

                var results = ""
                    for (var j = 0; j < searchers[i].results.length; j++) {
                        results += printer.draw(searchers[i].results[j])
                    }
                searchResults.innerHTML = results;
            }
    }
}


//--------------------------------------------------------------------------//


function onClick() {

    // Instatiate a GoogleSearcher
    gs = new GoogleSearcher()

        // Get the restrictions for this search
        restriction = document.searchForm.dependencies.options[document.searchForm.dependencies.selectedIndex].value

        // Find out the google searcher to use
        content_type = null;
    for (i=0; i < document.searchForm.content_type.length; i++)
    {
        if (document.searchForm.content_type[i].checked)
            content_type = document.searchForm.content_type[i].value;
    }
    if (content_type == 0)
        gs.addSearcher(gs.WEB_SEARCH, restriction)
            if (content_type == 1)
                gs.addSearcher(gs.IMAGE_SEARCH, restriction)
                    if (content_type == 2)
                        gs.addSearcher(gs.VIDEO_SEARCH, restriction)

                            // Execute the search
                            gs.setSearchResults("searchResults")
                            gs.execute(document.searchForm.query.value)
}

//]]>
</script>
