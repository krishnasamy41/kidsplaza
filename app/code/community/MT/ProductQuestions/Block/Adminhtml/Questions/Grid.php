<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category     MT
 * @package      MT_ProductQuestions
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       MagentoThemes.net
 * @email        support@magentothemes.net
 * ------------------------------------------------------------------------------
 *
 */
Class MT_ProductQuestions_Block_Adminhtml_Questions_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $session = Mage::getSingleton('adminhtml/session');
        $this->setId('questionsGrid');
        $this->setDefaultSort('question_id');
        $this->setDefaultSort('question_date')
             ->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        if (!is_string($session->getFilter()))
            $this->setDefaultFilter(array('question_replied' => ($this->getRequest()->getParam('id') ? '' : '0')));
    }

    protected function _prepareCollection(){
        $collection = Mage::getModel('productquestions/productquestions')->getCollection();
        $collection->getSelect()
            ->columns(array('question_replied' => new Zend_Db_Expr('if(parent_question_id>0,1,0)')));
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $collection->addFieldToFilter('question_product_id', $id);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('question_date', array(
            'header' => $this->__('Date'),
            'index' => 'question_date',
            'type' => 'date',
            'format' => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'align' => 'right',
            'width' => '150px',
        ));

        $this->addColumn('question_replied', array(
            'index' => 'question_replied',
            'header' => $this->__('Replied'),
            'type' => 'options',
            'options' => array(
                0 => $this->__('Questions'),
                1 => $this->__('Answer')
            ),
            'filter_condition_callback' => array($this, '_filterReplied'),
        ));

        $this->addColumn('question_author_name', array(
            'index' => 'question_author_name',
            'header' => $this->__('Author name'),
        ));

        $this->addColumn('question_author_email', array(
            'index' => 'question_author_email',
            'header' => $this->__('Email'),
        ));

        $this->addColumn('question_text', array(
            'index' => 'question_text',
            'header' => $this->__('Question text'),
            'width' => '250px',
        ));

        $this->addColumn('question_product_name', array(
            'index' => 'question_product_name',
            'header' => $this->__('Product title'),
            'width' => '250px',
        ));

        $this->addColumn('status', array(
            'index' => 'question_status',
            'header' => $this->__('Visibility'),
            'type' => 'options',
            'options' => MT_ProductQuestions_Model_Source_Question_Status::toShortOptionArray(),
            'align' => 'left',
            'width' => '80px',
        ));
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('asked_from', array(
                'type' => 'store',
                'header' => $this->__('Asked from'),
                'align' => 'left',
                'width' => '100px',
                'index' => 'question_store_id',
            ));
        }

        $this->addColumn('action', array(
            'header' => $this->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => $this->__('Reply/Edit'),
                    'url' => array('base' => '*/*/reply'),
                    'field' => 'id'
                ),
                array(
                    'caption' => $this->__('Delete'),
                    'url' => array('base' => '*/*/delete'),
                    'field' => 'id',
                    'confirm' => Mage::helper('productquestions')->__('Are you sure?')
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => '`main_table`.`question_id`',
            'is_system' => true,
        ));


        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {

        if (!($this->getRequest()->getParam('id'))) {
            $this->setMassactionIdField('`main_table`.`question_id`');
            $this->getMassactionBlock()->setFormFieldName('productquestions');

            $this->getMassactionBlock()->addItem('delete', array(
                'label' => $this->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete', array('ret' => $this->getRequest()->getActionName())),
                'confirm' => $this->__('Are you sure?')
            ));

            $statuses = MT_ProductQuestions_Model_Source_Question_Status::toOptionArray();

            array_unshift($statuses, array('label' => '', 'value' => ''));
            $this->getMassactionBlock()->addItem('question_status', array(
                'label' => $this->__('Change status'),
                'url' => $this->getUrl('*/*/massStatus', array('_current' => true, 'ret' => $this->getRequest()->getActionName())),
                'additional' => array(
                    'visibility' => array(
                        'name' => 'question_status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => $this->__('Status'),
                        'values' => $statuses
                    )
                )
            ));
            return $this;
        }
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/adminhtml_questions/edit', array(
            'id' => $row->getQuestionId()
        ));
    }

    protected function _filterReplied($collection, $column) {
        if($column->getFilter()->getValue()>0)
            return $collection->getSelect()->having('parent_question_id!=?', 0);
        else return $collection->getSelect()->having('parent_question_id=?', $column->getFilter()->getValue());
    }

}