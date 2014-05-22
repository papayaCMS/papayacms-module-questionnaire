<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE refentry PUBLIC "-//OASIS//DTD DocBook XML V4.4//EN"
"http://www.oasis-open.org/docbook/xml/4.4/docbookx.dtd">
<refentry id="{@id}">


  <refnamediv>
    <refname>tutorial PapayaQuestionnaireAnswerDatabaseAccess</refname>

    <refpurpose>Enable the reader to understand purpose and structure of the
    class.</refpurpose>
  </refnamediv>



  <refsynopsisdiv>
    <author>papaya Software GmbH</author>
  </refsynopsisdiv>



  <refsect1 id="{@id wlab-questionnaire-group-page}">
    <title>Purpose</title>

    <para>The <literal>PapayaQuestionnaireAnswerDatabaseAccess</literal> class is the reference
    and default implementation for the storage and retrieval of survey answers as exposed in the
    <literal>PapayaQuestionnaireAnswer</literal> class using the papaya CMS database abstraction
    layer.</para>
  </refsect1>

  <refsect1 id="{@id wlab-questionnaire-group-page}">
    <title>Structure</title>

    <para>The class is derived from the papaya CMS <literal>base_db class</literal>, allowing it
    to access different database engines according to the papaya configuration. For test purposes,
    the database object itself, a public property named <literal>databaseObject</literal> inherited
    from the <literal>base_db</literal> parent class, can be exchanged by simple assignment.</para>

    <para>There are only two properties the class defines on its own:
    <literal>_configuration</literal> is a <literal>PapayaConfiguration</literal> instance set on
    initialization, providing the papaya database table prefix and other configuration data.
    <literal>_tableAnswer</literal> is the name of the datbase table the answers are stored in.
    </para>

    <para>The methods share the names of those in <literal>PapayaQuestionnaireAnswer</literal>, see
    its documentation for details. The <literal>PapayaQuestionnaireAnswer</literal> class' methods
    invoke the methods of the same names in this class (or in any other storage implementation
    you happen to set for them to use). The only extra piece of data stored and retrieved by the
    methods in this class is a timestamp for each answer, inserted and updated along with the
    answer data.</para>

  </refsect1>

</refentry>
