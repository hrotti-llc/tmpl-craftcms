<?php

namespace hrotti\kernal\helpers;

use Craft;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;

use Lcobucci\JWT\Validation\Constraint\IdentifiedBy;

trait SecurityHelper {

	public function getProjectSecretKey(): string {
	
		return Craft::$app->getConfig()->getGeneral()->securityKey;

	}
	
	public function encrypt(
		string $value, 
		string $salt = null, 
		bool $baseEncode = true
	) {

		$encrypted = \Craft::$app->getSecurity()->encryptByKey($value, $this->getProjectSecretKey().$salt);

		return $baseEncode ? base64_encode($encrypted) : $encrypted;

	}

	public function getJWTRequest(
		$request
	) {

		return new JWTRequest($request);

	}

	public function isAuthorizedAPIRequest(
		$request
	) {

		$this->auth = $this->getJWTRequest($request);

		if ($this->auth->validate()) {
			
			return true;

		} else {

			return false;

		}

	}

	public function getCsrfName() {

		return Craft::$app->config->general->csrfTokenName;

	}

	public function getCurrentCsrfToken() {

		return Craft::$app->request->getCsrfToken();

	}

	public function getNewCsrfToken() {

		return Craft::$app->tokens->createToken('users/login');

	}

	public function getCsrfBasics() {

		return [
			'name' => $this->getCsrfName(),
			'token' => $this->getCurrentCsrfToken()
		];

	}

}

class JWTRequest {

	public $config;
	public $request;

	public $errors = [];

	public $isValid = null;

	public function __construct(
		$request, 
		$config = null
	) {

		$this->request = $request;
		$this->config = $config ?? $this->configure();

	}

	public function getSecretKey() : string {
		
		return getenv('JWT_SECRET_KEY');

	}

	public function configure(
		$symmetric = true
	) {

		$config = null;

		if ($symmetric) {

			$config = Configuration::forSymmetricSigner(
				new Sha256(),
				InMemory::base64Encoded(base64_encode($this->getSecretKey()))
			);

		}

		return $config;

	}

	public function issue() {

		$now = new \DateTimeImmutable();

		$token = $this->config->builder()
			->issuedBy(getenv("PRIMARY_SITE_URL"))
			->permittedFor(getenv("PRIMARY_SITE_URL"))
			->issuedAt($now)
			->expiresAt($now->modify('+2 hours'))
			->identifiedBy(getenv('APP_ID'))
			->getToken($this->config->signer(), $this->config->signingKey());

			$token->headers()->all();

		return $token;

	}

	public function parse() {

		$token = $this->config->parser()->parse($this->request->headers->get('authorization'));

		$this->config->setValidationConstraints(
			new \Lcobucci\JWT\Validation\Constraint\PermittedFor($this->request->headers->get('host')),
			new \Lcobucci\JWT\Validation\Constraint\IdentifiedBy(getenv('APP_ID')),
			new \Lcobucci\JWT\Validation\Constraint\SignedWith($this->config->signer(), $this->config->verificationKey()),
		);

		return $token;

	}

	public function validate() {

		if ($this->request->headers->has('authorization')) {

			$token = $this->parse();

			$constraints = $this->config->validationConstraints();

			try {

				$this->config->validator()->assert($token, ...$constraints);

				$this->isValid = true;

			} catch (RequiredConstraintsViolated $E) {

				$this->errors = $E->violations();

				$this->isValid = false;

			}

		} else {

			$this->errors = ["No authorization bearer provided."];

			$this->isValid = false;
		}

		return $this->isValid;

	}

}