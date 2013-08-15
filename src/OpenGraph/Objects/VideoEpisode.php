<?php namespace OpenGraph\Objects;

/**
 * @link http://ogp.me/#type_video.episode Video episode
 */
class VideoEpisode extends ObjectBase {
	/**
	 * URL of a video.tv_show which this episode belongs to
	 * @var string
	 */
	protected $series;

	/**
	 * URL of a video.tv_show which this episode belongs to
	 */
	public function getSeries() {
		return $this->series;
	}

	/**
	 * Set the URL of a video.tv_show which this episode belongs to
	 *
	 * @param string $url URL of a video.tv_show
	 */
	public function setSeries( $url ) {
		if ( static::is_valid_url($url) )
			$this->series = $url;
		return $this;
	}
}