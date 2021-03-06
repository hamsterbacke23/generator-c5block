<?php
namespace Concrete\Package\<%=pkgcchandle%>\Block\<%=blockcchandle%>;
use \Concrete\Core\Block\BlockController;
use Page;
use Loader;

/**
 * <%=blockcchandle%>
 *
 * @author <%=author%>
 * @since  <%=pkgversion%>
 */
<% if(om){ %>
class Controller extends OneToManyController {
<% } else { %>
class Controller extends BlockController {
<% } %>

    protected $btTable                              = 'bt<%=blockcchandle%>';
    protected $btInterfaceWidth                     = "450";
    protected $btInterfaceHeight                    = "450";
    protected $btWrapperClass                       = 'ccm-ui';
    protected $btCacheBlockRecord                   = true;
    protected $btCacheBlockOutput                   = true;
    protected $btCacheBlockOutputOnPost             = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    <% if(om){ %>
    //one to many stuff
    protected $omTable      = 'bt<%=blockcchandle%>Content';
    protected $omKey        = 'omcontents';
    // protected $omCheckboxes = array('displayIcon','displayCaption'); // edit and uncomment me if you use checkboxes!
    <% } %>

    /**
     * Get Block Name
     *
     * @return String Block Name
     */
    public function getBlockTypeName()
    {
        return t('<%=pkghandle%>.<%=blockhandle%>.blocktypename');
    }

    /**
     * Get Block Description
     *
     * @return String Block Description
     */
    public function getBlockTypeDescription()
    {
        return t('<%=pkghandle%>.<%=blockhandle%>.blocktypedescription');
    }

    <% if(om || tiny){ %>
    /**
     * Get Package Handle
     *
     * @return String Package Handle
     */
    public function getPkgHandle()
    {
        if (!isset($this->pkgHandle) || !$this->pkgHandle) {
            $blockType = BlockType::getByHandle($this->btHandle);
            $this->pkgHandle = $blockType->getPackageHandle();
        }
        return $this->pkgHandle;
    }
    <% } %>

    <% if(om){ %>
    /**
     * Set one to many form on edit
     *
     * @return null
     */
    public function edit()
    {
        $this->setOmForm();
    }

    /**
     * Set one to many form on add
     *
     * @return null
     */
    public function add()
    {
        $this->setOmForm();
    }
    <% } %>

    <% if(om || image){ %>
    /**
     * Set one to many content on view
     *
     * @return null
     */
    public function view()
    {
        <% if(om){ %>
        $this->setOmContent();
        <% } %>

    }
    <% } %>


    <% if(tiny || datetime || checkbox){ %>
    /**
     * Save Override
     *
     * @param array $args Arguments
     *
     * @return null
     */
    public function save($args)
    {
        <% _.each(_.uniq(checkboxes), function(cbkey) { %>
        $args['<%=cbkey%>'] = isset($args['<%=cbkey%>']) && $args['<%=cbkey%>'] == 'on' ? 1 : 0;
        <% }); %>

        <% if(tiny){ %>
        $tiny = new <%=blockcchandle%>Tiny();
        <% _.each(_.uniq(tinys), function(tinykeyb) { %>
        if (isset($args['<%=tinykeyb%>'])) {
            $<%=tinykeyb%> = $tiny->translateTo($args['<%=tinykeyb%>']);
            $args['<%=tinykeyb%>'] = $<%=tinykeyb%>; //normal c5 translate
        }
        <% }); %>

        <% if(om){ %>
        foreach ($args[$this->omKey] as $key => $item) {
        <% _.each(_.uniq(tinys), function(tinykeyb) { %>
            if(isset($args[$this->omKey][$key]['<%=tinykeyb%>'])) {
              $args[$this->omKey][$key]['<%=tinykeyb%>'] = $tiny->translateTo($item['<%=tinykeyb%>']); //row tiny c5 translate
            }
        <% }); %>
        }
        <% } //om true %>

        <% } //tiny true %>

        <% if(datetime){ %>
        $dtt = Loader::helper('form/date_time');
        <% _.each(_.uniq(datetimes), function(datetimekey) { %>
        $args['<%=datetimekey%>'] = $dtt->translate('<%=datetimekey%>');
        <% }); %>
        <% } //datetime true %>

        parent::save($args);
    }
    <% } //tiny or datetime true %>

    <% if(requiredFields){ %>
    /**
     * Validation
     *
     * @param array $args arguments to be validated
     *
     * @return $e Error Object
     */
    public function validate($args)
    {
        $e = Loader::helper('validation/error');

        <% _.each(fields, function(field) { %>
        <% if(field.required) { %>
        if (!trim($args['<%=field.key%>'])) {
            $e->add(t('<%=blockhandle%>.error.<%=field.key%>-missing'));
        }
        <% } %>
        <% }); %>

        <% if(om){ %>
        foreach ($args[$this->omKey] as $item) {
            if($item['delete'] == 'yes') {
                continue;
            }
            <% _.each(omfields, function(omfield) { if(typeof omfield != 'undefined' && omfield.required) { %>
            if (!trim($item['<%=omfield.key%>'])) {
                $e->add(t('<%=blockhandle%>.error.<%=omfield.key%>-missing'));
            }
            <% } });%>
        }
        <% } %>
        return $e;
    }
    <% } //requiredFields %>


    <% if(image){ %>
    /**
     * Build image
     *
     * @param integer $fID   File ID
     * @param array   $attrs Attribute
     *
     * @return object $img Image Object
     */
    public function buildImage($fID, $attrs = false)
    {
        $ih = Loader::helper('image_builder', 'sb_images');
        // $ih->addAssets($this);
        $img = $ih->getImageFromId($fID, $attrs);

        return $img;
    }
    <% } %>



    <% if(download){ %>
    /**
     * File Data
     *
     * @return array File Data Array
     */
    public function getFileData($fID)
    {
      $fileObject = File::getByID($fID);
      $fileObjectApproved = $fileObject->getApprovedVersion();
      $fsize = $fileObjectApproved->getFullSize();
      return array(
        'fileObject' => $fileObject,
        'size' => $this->formatBytes($fsize, 0),
        'fdate' => $fileObjectApproved->getDateAdded(),
        'ftype' =>  $fileObjectApproved->getType(),
        'linkText' =>  $fileObjectApproved->getFileName()
      );
    }

    /**
     * Format File
     *
     * @return String File Size
     */
    function formatBytes($bytes, $precision = 2)
    {
        $units = array(
            'Bytes',
            'Kilobytes',
            'Megabytes',
            'Gigabytes',
            'Terabytes'
        );
        $unitKeys = array(
            'b',
            'kb',
            'mb',
            'gb',
            'tb'
        );
        $base = log($bytes) / log(1024);
        $resultString = round(pow(1024, $base - floor($base)), $precision) . ' <abbr title="' . $units[floor($base)] . '">' . $unitKeys[floor($base)] . '</abbr>';
        return $resultString;
    }
    <% } %>


    <% if(linkintern){ %>
    /**
     * Creates a link from the cid
     *
     * @param Integer $bcID collection ID
     *
     * @return array Link
     */
    public function getPageLink($bcID)
    {
        Loader::model('internal/link', 'sb_links');
        $linkInfo = new LinkInternInfo($bcID);

        $link = array(
            'url'             => $linkInfo->getPageUrl(),
            'showError'       => $linkInfo->showLinkError(),
            'active'          => $linkInfo->isActive(),
            'errorMsg'        => $linkInfo->getLinkError(),
            'displayLinkText' => $linkInfo->getPageTitle(),
            'target'          => $this->target,
            'title'           => $this->title,
            'linkType'        => 'intern',
            'class'           => 'btn'
        );
        return $link;
    }
    <% } %>
}



