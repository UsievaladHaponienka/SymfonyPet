<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

class BaseType extends AbstractType
{
    public function getFormClass(): string
    {
        return 'bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 w-full';
    }

    public function getTextInputClass(): string
    {
        return 'shadow appearance-none border rounded w-full py-2 px-3 ' .
            'text-gray-700 leading-tight focus:outline-none focus:shadow-outline';
    }

    public function getFileInputClass(): string
    {
        return 'appearance-none rounded w-full py-2 px-3 ' .
            'text-gray-700 leading-tight focus:outline-none focus:shadow-outline';
    }

    public function getLabelClass(): string
    {
        return 'block text-gray-500 font-bold md:text-left mb-1 md:mb-0 pr-4';
    }

    public function getSubmitButtonClass(): string
    {
        return 'bg-indigo-500 hover:bg-indigo-400 focus:shadow-outline focus:outline-none text-white font-bold mt-4 py-2 px-4 rounded';
    }
}
