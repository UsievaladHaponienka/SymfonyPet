<?php

namespace App\Form;

trait FormStyle
{
    /**
     * @return string
     */
    public function getFormClass(): string
    {
        return 'bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 w-full';

    }

    /**
     * @return string
     */
    public function getTextInputClass(): string
    {
        return 'shadow appearance-none border rounded w-full py-2 px-3 ' .
            'text-gray-700 leading-tight focus:outline-none focus:shadow-outline';
    }

    /**
     * @return string
     */
    public function getLabelClass(): string
    {
        return 'block text-gray-500 font-bold md:text-left mb-1 md:mb-0 pr-4';
    }

    public function getSubmitButtonClass(): string
    {
        return 'bg-lime-500 hover:bg-lime-400 focus:shadow-outline focus:outline-none text-white font-bold mt-4 py-2 px-4 rounded';
    }
}
