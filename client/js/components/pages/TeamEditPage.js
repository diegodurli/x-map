import React, { Component, PropTypes } from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import * as TeamActions from '../../actions/TeamActions';
import TeamForm from '../forms/TeamForm';

class TeamEditPage extends Component {

  redirectToTeamPage(id) {
    this.props.history.pushState(null, '/team/' + id);
  }

  render() {
    const { actions, errors, teams, params, history } = this.props;

    const team = teams[params.id];
    if (!team) {
      history.pushState(null, '/404');
      return <span/>;
    }

    return (
      <div className="panel">
        <article>
          <section>
            <TeamForm team={team} onSubmit={actions.teamUpdate} onSuccess={this.redirectToTeamPage.bind(this, params.id)} errors={errors}/>
          </section>
        </article>
      </div>
    );
  }
}

TeamEditPage.propTypes = {
  teams: PropTypes.object.isRequired,
  params: PropTypes.shape({
    id: PropTypes.string.isRequired
  }).isRequired,
  history: PropTypes.object.isRequired,
  actions: PropTypes.object,
  errors: PropTypes.shape({
    globalErrors: PropTypes.array,
    fieldErrors: PropTypes.object
  })
};

function mapStateToProps(state) {
  return {
    teams: state.teams,
    errors: state.errors.teamUpdate
  };
}

function mapDispatchToProps(dispatch) {
  return {
    actions: bindActionCreators(TeamActions, dispatch)
  };
}

export default connect(mapStateToProps, mapDispatchToProps)(TeamEditPage);
