<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE refentry PUBLIC "-//OASIS//DTD DocBook XML V4.4//EN"
"http://www.oasis-open.org/docbook/xml/4.4/docbookx.dtd">
<refentry id="{@id}">
  <refnamediv>
    <refname>tutorial PapayaQuestionnaireAnswer</refname>

    <refpurpose>Enable the reader to understand purpose and structure of the
    class.</refpurpose>
  </refnamediv>

  <refsynopsisdiv>
    <author>papaya Software GmbH</author>
  </refsynopsisdiv>

  <refsect1 id="{@id wlab-questionnaire-group-page}">
    <title>Purpose</title>

    <para>The <literal>PapayaQuestionnaireAnswer</literal> class provides
    methods to store and retrieve questionnaire answers by users. An answer
    consists of a question id referring to the question being answered, a user
    id referring to the user answering it, a subject id referring to the
    subject of the question if the questionnaire allows the questions to be
    answered for an arbitrary number of subjects (e.g. multiple physicians),
    and, obviously, the answer value itself. Besides, answers can be
    deactivated (and reactivated) both by user and by subject id because you
    might want to exclude a specified user and/or subject from the survey
    results.</para>
  </refsect1>

  <refsect1 id="{@id wlab-questionnaire-group-page}">
    <title>Structure</title>

    <para>The relevant attributes of
    <literal>PapayaQuestionnaireAnswer</literal> are
    <literal>_databaseAccessObject</literal> and
    <literal>_configuration</literal>. The
    <literal>_databaseAccessObject</literal> is the object that provides the
    real data storage logic for each method, its current implementation is the
    <literal>PapayaQuestionnaireAnswerDatabaseAccess</literal> class that can
    be exchanged using dependency injection both for unit testing and for
    using an alternative storage mechanism. The
    <literal>_configuration</literal> is a PapayaConfiguration instance passed
    through after instantiation of a
    <literal>PapayaQuestionnaireAnswer</literal> object. It is in turn passed
    to the <literal>_databaseAccessObject</literal> if you stick with the
    reference implementation, the above mentioned
    <literal>PapayaQuestionnaireAnswerDatabaseAccess</literal> class.</para>

    <para>Methods can be divided into two general types: infrastructure
    methods to set up the environment and service methods for the services the
    class provides to other parts of the Questionnaire package.</para>

    <para>The following infrastructure methods can be found in the
    class:</para>

    <itemizedlist>
      <listitem>
        <para><literal>setDatabaseAccessObject</literal> can be used to set an
        alternative <literal>_databaseAccessObject</literal></para>
      </listitem>

      <listitem>
        <para><literal>getDatabaseAccessObject</literal> uses lazy
        initialization to either simply return the
        <literal>_databaseAccessObject</literal> set or initialized before, or
        to create a <literal>PapayaQuestionnaireAnswerDatabaseAccess</literal>
        instance and call its <literal>setConfiguration</literal> method if no
        other object has been set before.</para>
      </listitem>

      <listitem>
        <para><literal>setConfiguration</literal> is used to pass the
        <literal>PapayaConfiguration</literal> object after instantiation of this
        class</para>
      </listitem>
    </itemizedlist>

    <para>As for the service methods, you can use the following:</para>

    <itemizedlist>
      <listitem>
        <para><literal>getActiveAnswersByUserAndSubject</literal> returns the
        non-deactivated answers by a user (specified by user id) for a subject
        (specified by subject id). Optionally, you can provide an array of
        question ids as a third parameter if you only need answers for some of
        the questions (typically, this will be used when questions are
        presented using an extra page for each group). The return value has
        got the structure <literal>array($questionId =&gt; $answer,
        ...)</literal>. If no answers are available, an empty array is
        returned.</para>
      </listitem>

      <listitem>
        <para><literal>getAnswersByUserAndSubject</literal> works much like
        <literal>getActiveAnswersByUserAndSubject</literal>, but returns the
        answers regardless of whether they are deactivated or not. The
        structure of the return value is <literal>array($questionId =&gt;
        array( 'answer_value' =&gt; $value, 'answer_deactivated' =&gt;
        $timestamp), ...)</literal> or an empty arrow if no answers can be
        found.</para>
      </listitem>

      <listitem>
        <para><literal>getAnswersBySubjectId</literal> is most interesting for
        the survey's final analysis: It returns the answers of all users for a
        specified subject id. The normal structure is
        <literal>array($questionId =&gt; array($userId =&gt; $answer, ...),
        ...)</literal>, but if you set the second, optional parameter to TRUE,
        it will switch to <literal>array($userId =&gt; array($questionId =&gt;
        $answer, ...), ...)</literal>, grouping the answers by user id
        first.</para>
      </listitem>

      <listitem>
        <para><literal>getActiveSubjectIdsByUser</literal> returns an array
        of subjects a user has provided active answers for. The keys are subject ids and
        the values are answer timestamps. This is useful to create a list of subjects
        for the user to review or change their answers.</para>
      </listitem>

      <listitem>
        <para><literal>saveAnswersByUserAndSubject</literal> stores a set of
        answers a specified user submitted for a specified subject. The three
        parameters are user id, subject id, and an associative array in which
        the keys are question ids and the values their respective answers. The
        method will automatically replace existing answers for the same
        questions if there are any.</para>
      </listitem>

      <listitem>
        <para><literal>deactivateAnswersByUserId</literal> deactivates all
        answers by the specified user, e.g. on abuse.</para>
      </listitem>

      <listitem>
        <para><literal>activateAnswersByUserId</literal> reactivates a user's
        answers.</para>
      </listitem>

      <listitem>
        <para><literal>deactivateAnswersBySubjectId</literal> deactivates all
        answers for the specified subject, suitable if this particular subject
        is no longer part of the survey.</para>
      </listitem>

      <listitem>
        <para><literal>activateAnswersBySubjectId</literal> reactivates all
        answers for a subject.</para>
      </listitem>
    </itemizedlist>
  </refsect1>
</refentry>
