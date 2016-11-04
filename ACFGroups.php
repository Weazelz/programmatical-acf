<?php

namespace ACF;

/**
 * Holds all ACF group classes for reference
 *
 * Class ACFGroups
 * @package Theme\Helpers\ACF
 */
class ACFGroups
{

	protected $groups = [];

    public function __construct()
    {
        global $acfGroups;
        $acfGroups = $this;
    }

    function addGroup(ACFGroup $group)
	{
		$this->groups[$group->getGroupName()] = $group;
	}

	function getOptionPageGroupNames()
	{
		$names = [];

		foreach ($this->groups as $name => $group) {

			if ($group->isOptionPage()) {
				$names[] = $name;
			}
		}

		return $names;
	}
}
