<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\Utilities;

/**
 * Class PropertyAnnotation
 *
 * @package hollodotme\Utilities
 */
final class PropertyAnnotation
{

	const ACCESS_READ  = 1;

	const ACCESS_WRITE = 2;

	/** @var string */
	private $type;

	/** @var string */
	private $name;

	/** @var string */
	private $comment;

	/** @var int */
	private $accessability;

	/**
	 * @param string $name
	 * @param string $type
	 * @param int    $accessability
	 * @param string $comment
	 */
	private function __construct( $name, $type, $accessability, $comment )
	{
		$this->name          = $name;
		$this->type          = $type;
		$this->accessability = $accessability;
		$this->comment       = $comment;
	}

	/**
	 * @return bool
	 */
	public function isReadable()
	{
		return (bool)($this->accessability & self::ACCESS_READ);
	}

	/**
	 * @return bool
	 */
	public function isWritable()
	{
		return (bool)($this->accessability & self::ACCESS_WRITE);
	}

	/**
	 * @return bool
	 */
	public function isReadOnly()
	{
		return ($this->isReadable() && !$this->isWritable());
	}

	/**
	 * @return bool
	 */
	public function isWriteOnly()
	{
		return ($this->isWritable() && !$this->isReadable());
	}

	/**
	 * @return int
	 */
	public function getAccessability()
	{
		return $this->accessability;
	}

	/**
	 * @return string
	 */
	public function getComment()
	{
		return $this->comment;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string $doc_comment_string
	 *
	 * @return PropertyAnnotation|null
	 */
	public static function fromDocCommentString( $doc_comment_string )
	{
		$matches            = [ ];
		$doc_comment_string = preg_replace( "#\s+#", ' ', $doc_comment_string );

		if ( preg_match( "#^.*@property-?(read|write)? (\w+) \\$(\w+) ?(.*)?$#i", $doc_comment_string, $matches ) )
		{
			if ( !strcasecmp( $matches[1], 'read' ) )
			{
				$accessability = self::ACCESS_READ;
			}
			elseif ( !strcasecmp( $matches[1], 'write' ) )
			{
				$accessability = self::ACCESS_WRITE;
			}
			else
			{
				$accessability = (self::ACCESS_READ | self::ACCESS_WRITE);
			}

			return new self( $matches[3], $matches[2], $accessability, $matches[4] );
		}
		else
		{
			return null;
		}
	}
}
