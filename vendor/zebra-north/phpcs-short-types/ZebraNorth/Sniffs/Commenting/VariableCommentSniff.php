<?php

/**
 * This is a minor modification of Squiz.Commenting.VariableComment.
 * Unfortunately the entire code has to be copy/pasted.
 */

namespace PHP_CodeSniffer\Standards\ZebraNorth\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Commenting\VariableCommentSniff as SquizVariableCommentSniff;
use PHP_CodeSniffer\Util\Common;
use PHP_CodeSniffer\Util\Tokens;

class VariableCommentSniff extends SquizVariableCommentSniff
{


    /**
     * Called to process class member vars.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function processMemberVar(File $phpcsFile, int $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $ignore  = Tokens::SCOPE_MODIFIERS;
        $ignore += Tokens::NAME_TOKENS;
        $ignore += [
            T_VAR                    => T_VAR,
            T_STATIC                 => T_STATIC,
            T_READONLY               => T_READONLY,
            T_FINAL                  => T_FINAL,
            T_ABSTRACT               => T_ABSTRACT,
            T_WHITESPACE             => T_WHITESPACE,
            T_NULLABLE               => T_NULLABLE,
            T_TYPE_UNION             => T_TYPE_UNION,
            T_TYPE_INTERSECTION      => T_TYPE_INTERSECTION,
            T_TYPE_OPEN_PARENTHESIS  => T_TYPE_OPEN_PARENTHESIS,
            T_TYPE_CLOSE_PARENTHESIS => T_TYPE_CLOSE_PARENTHESIS,
            T_NULL                   => T_NULL,
            T_TRUE                   => T_TRUE,
            T_FALSE                  => T_FALSE,
            T_SELF                   => T_SELF,
            T_PARENT                 => T_PARENT,
        ];

        for ($commentEnd = ($stackPtr - 1); $commentEnd >= 0; $commentEnd--) {
            if (isset($ignore[$tokens[$commentEnd]['code']]) === true) {
                continue;
            }

            if (
                $tokens[$commentEnd]['code'] === T_ATTRIBUTE_END
                && isset($tokens[$commentEnd]['attribute_opener']) === true
            ) {
                $commentEnd = $tokens[$commentEnd]['attribute_opener'];
                continue;
            }

            break;
        }

        if (
            $tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG
            && $tokens[$commentEnd]['code'] !== T_COMMENT
        ) {
            $phpcsFile->addError('Missing member variable doc comment', $stackPtr, 'Missing');
            return;
        }

        if ($tokens[$commentEnd]['code'] === T_COMMENT) {
            $phpcsFile->addError('You must use "/**" style comments for a member variable comment', $stackPtr, 'WrongStyle');
            return;
        }

        $commentStart = $tokens[$commentEnd]['comment_opener'];

        $foundVar = null;
        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            if ($tokens[$tag]['content'] === '@var') {
                if ($foundVar !== null) {
                    $error = 'Only one @var tag is allowed in a member variable comment';
                    $phpcsFile->addError($error, $tag, 'DuplicateVar');
                } else {
                    $foundVar = $tag;
                }
            } elseif ($tokens[$tag]['content'] === '@see') {
                // Make sure the tag isn't empty.
                $string = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $tag, $commentEnd);
                if ($string === false || $tokens[$string]['line'] !== $tokens[$tag]['line']) {
                    $error = 'Content missing for @see tag in member variable comment';
                    $phpcsFile->addError($error, $tag, 'EmptySees');
                }
            } else {
                $error = '%s tag is not allowed in member variable comment';
                $data  = [$tokens[$tag]['content']];
                $code  = ucwords(ltrim($tokens[$tag]['content'], '@')) . 'TagNotAllowed';
                $phpcsFile->addWarning($error, $tag, $code, $data);
            }
        }

        // The @var tag is the only one we require.
        if ($foundVar === null) {
            $error = 'Missing @var tag in member variable comment';
            $phpcsFile->addError($error, $commentEnd, 'MissingVar');
            return;
        }

        $firstTag = $tokens[$commentStart]['comment_tags'][0];
        if ($foundVar !== null && $tokens[$firstTag]['content'] !== '@var') {
            $error = 'The @var tag must be the first tag in a member variable comment';
            $phpcsFile->addError($error, $foundVar, 'VarOrder');
        }

        // Make sure the tag isn't empty and has the correct padding.
        $string = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $foundVar, $commentEnd);
        if ($string === false || $tokens[$string]['line'] !== $tokens[$foundVar]['line']) {
            $error = 'Content missing for @var tag in member variable comment';
            $phpcsFile->addError($error, $foundVar, 'EmptyVar');
            return;
        }

        // Support both a var type and a description.
        preg_match('`^((?:\|?(?:array\([^\)]*\)|[\\\\a-z0-9\[\]]+))*)( .*)?`i', $tokens[($foundVar + 2)]['content'], $varParts);
        if (isset($varParts[1]) === false) {
            return;
        }

        $varType = $varParts[1];

        // Check var type (can be multiple, separated by '|').
        $typeNames      = explode('|', $varType);
        $suggestedNames = [];
        foreach ($typeNames as $typeName) {
            $suggestedName = $this->suggestType($typeName);
            if (in_array($suggestedName, $suggestedNames, true) === false) {
                $suggestedNames[] = $suggestedName;
            }
        }

        $suggestedType = implode('|', $suggestedNames);
        if ($varType !== $suggestedType) {
            $error = 'Expected "%s" but found "%s" for @var tag in member variable comment';
            $data  = [
                $suggestedType,
                $varType,
            ];
            $fix   = $phpcsFile->addFixableError($error, $foundVar, 'IncorrectVarType', $data);
            if ($fix === true) {
                $replacement = $suggestedType;
                if (empty($varParts[2]) === false) {
                    $replacement .= $varParts[2];
                }

                $phpcsFile->fixer->replaceToken(($foundVar + 2), $replacement);
                unset($replacement);
            }
        }
    }


    protected function suggestType(string $type)
    {
        $suggestion = Common::suggestType($type);

        if ($suggestion === 'integer') {
            return 'int';
        } else if ($suggestion === 'boolean') {
            return 'bool';
        }

        return $suggestion;
    }
}
