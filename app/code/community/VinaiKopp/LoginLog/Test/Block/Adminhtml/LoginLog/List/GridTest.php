<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this Module to
 * newer versions in the future.
 *
 * @category   Magento
 * @package    VinaiKopp_LoginLog
 * @copyright  Copyright (c) 2014 Vinai Kopp http://netzarbeiter.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class VinaiKopp_LoginLog_Test_Block_Adminhtml_LoginLog_List_GridTest
    extends EcomDev_PHPUnit_Test_Case
{
    protected $class = 'VinaiKopp_LoginLog_Block_Adminhtml_LoginLog_List_Grid';

    protected function prepareEnvironmentForGrid()
    {
        $helper = new VinaiKopp_LoginLog_Test_TestHelper;
        $helper->stubAdminhtmlSession();

        $stubUrl = $this->getModelMock('adminhtml/url');
        $this->replaceByMock('model', 'adminhtml/url', $stubUrl);
    }

    /**
     * @return EcomDev_PHPUnit_Mock_Proxy
     */
    protected function getLayoutStub()
    {
        $stubButton = $this->getMock('Mage_Adminhtml_Block_Widget_Button');
        $stubColumn = $this->getMock(
            'Mage_Adminhtml_Block_Widget_GridColumn',
            array('setData', 'setGrid', 'setId', 'getIndex')
        );
        $stubColumn->expects($this->any())
            ->method('setData')
            ->will($this->returnSelf());
        $stubColumn->expects($this->any())
            ->method('setGrid')
            ->will($this->returnSelf());
        
        $stubLayout = $this->getModelMock('core/layout');
        $stubLayout->expects($this->any())
            ->method('createBlock')
            ->withAnyParameters()
            ->will($this->returnValueMap(array(
                array('adminhtml/widget_button', '', array(), $stubButton),
                array('adminhtml/widget_grid_column', '', array(), $stubColumn)
            )));
        return $stubLayout;
    }
    
    /**
     * @return VinaiKopp_LoginLog_Block_Adminhtml_LoginLog_List_Grid
     */
    public function getInstance()
    {
        $stubMassAction = $this->getMock(
            'Mage_Adminhtml_Block_Widget_Grid_Massaction',
            array('setFormFieldName', 'addItem')
        );
        
        $instance = new $this->class;
        $instance->setChild('massaction', $stubMassAction);
        
        return $instance;
    }

    /**
     * @test
     */
    public function itShouldExist()
    {
        $this->assertTrue(class_exists($this->class));
    }

    /**
     * @test
     */
    public function itShouldHaveAGridUrl()
    {
        $this->prepareEnvironmentForGrid();

        $mockUrl = $this->getModelMock('adminhtml/url');
        $mockUrl->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo('*/*/grid'), $this->anything())
            ->will($this->returnValue('http://expectation'));
        $this->replaceByMock('model', 'adminhtml/url', $mockUrl);

        $block = $this->getInstance();
        
        $this->assertEquals('http://expectation', $block->getGridUrl());
    }

    /**
     * @test
     */
    public function itShouldHaveALoginLogCollection()
    {
        $this->prepareEnvironmentForGrid();

        $stubLayout = $this->getLayoutStub();
        
        $mockCollection = $this->getResourceModelMockBuilder('vinaikopp_loginlog/login_collection')
            ->disableOriginalConstructor()
            ->getMock();
        $this->replaceByMock('resource_model', 'vinaikopp_loginlog/login_collection', $mockCollection);
        
        $block = $this->getInstance();
        $block->setLayout($stubLayout);
        $block->toHtml();
        
        $this->assertSame($mockCollection, $block->getCollection());
    }

    /**
     * @test
     */
    public function itShouldHaveColumns()
    {
        $this->prepareEnvironmentForGrid();
        
        $stubLayout = $this->getLayoutStub();
        $block = $this->getInstance();
        $block->setLayout($stubLayout);
        $block->toHtml();
        
        $this->assertGreaterThan(1, $block->getColumnCount());
    }

    /**
     * @test
     */
    public function itShouldHaveAnOptionToExport()
    {
        $this->prepareEnvironmentForGrid();

        $stubLayout = $this->getLayoutStub();
        $block = $this->getInstance();
        $block->setLayout($stubLayout);
        $block->toHtml();

        $this->assertGreaterThan(0, $block->getExportTypes());
    }

    /**
     * @test
     */
    public function itShouldHaveADeleteMassAction()
    {
        $this->prepareEnvironmentForGrid();

        $stubLayout = $this->getLayoutStub();
        $block = $this->getInstance();
        
        $mockMassAction = $block->getChild('massaction');
        $mockMassAction->expects($this->once())
            ->method('setFormFieldName')
            ->with('logins');
        $mockMassAction->expects($this->once())
            ->method('addItem')
            ->with('delete');
        
        $block->setLayout($stubLayout);
        $block->toHtml();
    }
}