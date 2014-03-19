<?php

/**
 * Copyright (c) 2011-present Mediasift Ltd
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Libraries
 * @package   Storyplayer/OutputLib
 * @author    Stuart Herbert <stuart.herbert@datasift.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */

namespace DataSift\Storyplayer\OutputLib;

use DataSift\Stone\LogLib\Log;
use DataSift\StoryPlayer\Phases\Phase;
use DataSift\StoryPlayer\PlayerLib\StoryResult;

/**
 * the console plugin we use unless the user specifies something else
 *
 * @category  Libraries
 * @package   Storyplayer/OutputLib
 * @author    Stuart Herbert <stuart.herbert@datasift.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */
class DefaultConsolePlugin implements OutputPlugin
{
	protected $phaseNumber = 0;
	protected $phaseErrors = array();

	protected $verbosityLevel = 0;

	public function setVerbosity($verbosityLevel)
	{
		$this->verbosityLevel = $verbosityLevel;
	}

	public function startStoryplayer($version, $url, $copyright, $license)
	{
		echo <<<EOS
Storyplayer {$version} - {$url}
{$copyright}
{$license}


EOS;
	}

	public function endStoryplayer()
	{

	}

	public function startStory($storyName, $storyCategory, $storyGroup, $envName, $deviceName)
	{
		if ($this->verbosityLevel > 0) {
			echo <<<EOS
=============================================================

      Story: {$storyName}
   Category: {$storyCategory}
      Group: {$storyGroup}

Environment: {$envName}
     Device: {$deviceName}

EOS;
		}
		else {
			echo PHP_EOL . $storyName . ':';
		}


		// reset the phaseNumber counter
		$this->phaseNumber = 0;
	}

	public function endStory(StoryResult $storyResult)
	{
		// output any errors that we have
		echo PHP_EOL
		     . "This story failed with the following errors:"
		     . PHP_EOL . PHP_EOL;

		foreach ($this->phaseErrors as $phaseName => $msg)
		{
			echo $phaseName . ': ' . $msg . PHP_EOL;
		}
	}

	public function startStoryPhase($phaseName, $phaseType)
	{
		// we're only interested in telling the user about the
		// phases of a story
		if ($phaseType !== Phase::STORY_PHASE) {
			return;
		}

		// increment our internal counter
		$this->phaseNumber++;

		// tell the user which phase we're doing
		if ($this->verbosityLevel > 0) {
			echo PHP_EOL . $phaseName . ': ';
		}
		else {
			echo ' ' . $this->phaseNumber;
		}
	}

	public function endStoryPhase()
	{

	}

	public function logStoryActivity($level, $msg)
	{
		// this is a no-op for us
		echo ".";
	}

	public function logStoryError($phaseName, $msg)
	{
		// we have to show this now, and save it for final output later
		echo "E";

		$this->phaseErrors[$phaseName] = $msg;
	}

	public function logStorySkipped($phaseName, $msg)
	{
		// we have to show this now, and save it for final output later
		echo "S";

		$this->phaseErrors[$phaseName] = $msg;
	}

	public function logCliError($msg)
	{
		echo "*** error: $msg" . PHP_EOL;
	}

	public function logCliWarning($msg)
	{
		echo "*** warning: $msg" . PHP_EOL;
	}

	public function logCliInfo($msg)
	{
		echo $msg . PHP_EOL;
	}
}
