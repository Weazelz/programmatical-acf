<?php

namespace ACF;

class ACFRepeater extends ACFGroup
{
	/**
	 * Overrides default one, we dont want to register our field group but subfields instead
	 *
	 * ACFRepeater constructor.
	 */
	function __construct()
	{
	}

	function getSubfields()
	{
		return array_values($this->fields);
	}
}
