<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The HTML template mailer class
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Mailer;
	use Codelab\FlaskPHP as FlaskPHP;


	class HTMLMailer extends Mailer
	{


		/**
		 *   Template
		 *   @var string
		 *   @access public
		 */

		public $template=null;


		/**
		 *   Template variables
		 *   @var array
		 *   @access public
		 */

		public $templateVar=array();


		/**
		 *
		 *   Set template variable
		 *   ---------------------
		 *   @access public
		 *   @param string $variable Variable name
		 *   @param mixed $value Variable value
		 *   @throws \Exception
		 *   @return HTMLMailer
		 *
		 */

		public function setVariable( string $variable, $value )
		{
			// Remove
			if ($value===null)
			{
				unset($this->templateVar[$variable]);
			}

			// Set
			else
			{
				$this->templateVar[$variable]=$value;
			}

			// Return self
			return $this;
		}


		/**
		 *
		 *   Set template variables
		 *   ----------------------
		 *   @access public
		 *   @param array $variables Variables
		 *   @throws \Exception
		 *   @return HTMLMailer
		 *
		 */

		public function setVariables( array $variables )
		{
			// Set variables
			foreach ($variables as $k => $v)
			{
				$this->setVariable($k,$v);
			}

			// Return self
			return $this;
		}


		/**
		 *
		 *   Set content
		 *   -----------
		 *   @access public
		 *   @param string $content Content
		 *   @throws \Exception
		 *   @return HTMLMailer
		 *
		 */

		public function setContent( string $content )
		{
			// Set
			$this->setVariable('content',$content);

			// Return self
			return $this;
		}


		/**
		 *
		 *   Set template
		 *   ------------
		 *   @access public
		 *   @param string $template Template filename
		 *   @throws \Exception
		 *   @return HTMLMailer
		 *
		 */

		public function setTemplate( string $template )
		{
			$this->template=$template;
			return $this;
		}


		/**
		 *
		 *   Get template
		 *   ------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function getTemplate()
		{
			// Check
			if ($this->template===null) throw new FlaskPHP\Exception\Exception('Template not set.');

			// Check if template exists
			if ($this->template[0]=='/')
			{
				$templateFilename=$this->template;
			}
			else
			{
				$templateFilename=Flask()->resolvePath('template/mail.'.$this->template.'.tpl');
			}
			if (!is_readable($templateFilename)) throw new FlaskPHP\Exception\Exception('Template not found or not readable.');

			// Return
			return file_get_contents($templateFilename);
		}


		/**
		 *
		 *   Parse template
		 *   --------------
		 *   @access public
		 *   @param string $templateContent Template content
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function parseTemplate( string $templateContent )
		{
			$Template=new FlaskPHP\Template\Template();
			$Template->templateVar=$this->templateVar;
			$Template->templateContent=$templateContent;
			return $Template->parse();
		}


    /**
		 *
     *   Create a message and send it
		 *   ----------------------------
		 *   @access public
     *   Uses the sending method specified by $Mailer.
     *   @throws \Exception
     *   @return bool
		 *
     */

    public function send()
    {
    	// Prepare message body
			$messageContent=$this->parseTemplate($this->getTemplate());

			// Set message body
			parent::MsgHTML($messageContent);
			$this->AltBody=strip_tags(preg_replace("/<style>.*?<\/style>/Uim","",$messageContent));

			// Prepare subject
			$this->Subject=$this->parseTemplate($this->Subject);

    	// Send
			return parent::send();
    }


	}


?>