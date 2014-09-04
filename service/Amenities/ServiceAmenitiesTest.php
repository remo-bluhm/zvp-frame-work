<?php

require_once 'PHPUnit\Framework\TestCase.php';

/**
 * ServiceAmenities test case.
 */
class ServiceAmenitiesTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var ServiceAmenities
	 */
	private $ServiceAmenities;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated ServiceAmenitiesTest::setUp()
		require_once 'service/Amenities/ServiceAmenities.php';
		$this->ServiceAmenities = new ServiceAmenities(/* parameters */);
	
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated ServiceAmenitiesTest::tearDown()
		
		$this->ServiceAmenities = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests ServiceAmenities->__construct()
	 */
	public function test__construct() {
		// TODO Auto-generated ServiceAmenitiesTest->test__construct()
		$this->markTestIncomplete ( "__construct test not implemented" );
		
		$this->ServiceAmenities->__construct(/* parameters */);
	
	}
	
	/**
	 * Tests ServiceAmenities->ActionGetElementList()
	 */
	public function testActionGetElementList() {
		// TODO Auto-generated ServiceAmenitiesTest->testActionGetElementList()
		//$this->markTestIncomplete ( "ActionGetElementList test not implemented" );
		
		$ServiceListe = $this->ServiceAmenities->ActionGetElementList(44);
		$this->assertFalse(is_array($ServiceListe),"Es wurden keine Daten zurückgegeben");
// 		$this->assertArrayNotHasKey($key, $array)
// 		foreach ($)
	
	}
	
	/**
	 * Tests ServiceAmenities->newRoot()
	 */
	public function testNewRoot() {
		// TODO Auto-generated ServiceAmenitiesTest->testNewRoot()
		$this->markTestIncomplete ( "newRoot test not implemented" );
		
		$this->ServiceAmenities->newRoot(/* parameters */);
	
	}
	
	/**
	 * Tests ServiceAmenities->ActionNewElement()
	 */
	public function testActionNewElement() {
		// TODO Auto-generated ServiceAmenitiesTest->testActionNewElement()
		$this->markTestIncomplete ( "ActionNewElement test not implemented" );
		
		$this->ServiceAmenities->ActionNewElement(/* parameters */);
	
	}
	
	/**
	 * Tests ServiceAmenities->ActionNewTree()
	 */
	public function testActionNewTree() {
		// TODO Auto-generated ServiceAmenitiesTest->testActionNewTree()
		$this->markTestIncomplete ( "ActionNewTree test not implemented" );
		
		$this->ServiceAmenities->ActionNewTree(/* parameters */);
	
	}
	
	/**
	 * Tests ServiceAmenities->ActionEditElement()
	 */
	public function testActionEditElement() {
		// TODO Auto-generated ServiceAmenitiesTest->testActionEditElement()
		$this->markTestIncomplete ( "ActionEditElement test not implemented" );
		
		$this->ServiceAmenities->ActionEditElement(/* parameters */);
	
	}
	
	/**
	 * Tests ServiceAmenities->ActionDeleted()
	 */
	public function testActionDeleted() {
		// TODO Auto-generated ServiceAmenitiesTest->testActionDeleted()
		$this->markTestIncomplete ( "ActionDeleted test not implemented" );
		
		$this->ServiceAmenities->ActionDeleted(/* parameters */);
	
	}
	
	/**
	 * Tests ServiceAmenities->ActionDeletedGroup()
	 */
	public function testActionDeletedGroup() {
		// TODO Auto-generated ServiceAmenitiesTest->testActionDeletedGroup()
		$this->markTestIncomplete ( "ActionDeletedGroup test not implemented" );
		
		$this->ServiceAmenities->ActionDeletedGroup(/* parameters */);
	
	}
	
	/**
	 * Tests ServiceAmenities->ActionUp()
	 */
	public function testActionUp() {
		// TODO Auto-generated ServiceAmenitiesTest->testActionUp()
		$this->markTestIncomplete ( "ActionUp test not implemented" );
		
		$this->ServiceAmenities->ActionUp(/* parameters */);
	
	}
	
	/**
	 * Tests ServiceAmenities->ActionDown()
	 */
	public function testActionDown() {
		// TODO Auto-generated ServiceAmenitiesTest->testActionDown()
		$this->markTestIncomplete ( "ActionDown test not implemented" );
		
		$this->ServiceAmenities->ActionDown(/* parameters */);
	
	}
	
	/**
	 * Tests ServiceAmenities->ActionMove()
	 */
	public function testActionMove() {
		// TODO Auto-generated ServiceAmenitiesTest->testActionMove()
		$this->markTestIncomplete ( "ActionMove test not implemented" );
		
		$this->ServiceAmenities->ActionMove(/* parameters */);
	
	}

}

