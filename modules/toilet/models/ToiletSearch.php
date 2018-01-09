<?php

namespace app\modules\toilet\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\toilet\models\Toilet;

/**
 * ToiletSearch represents the model behind the search form about `app\modules\toilet\models\Toilet`.
 */
class ToiletSearch extends Toilet
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'webid', 'aid', 'shownum', 'ishidden', 'satisfyscore', 'usecount', 'finaldestid', 'issmarty'], 'integer'],
            [['title', 'seotitle', 'content', 'address', 'addtime', 'modtime', 'keyword', 'description', 'tagword', 'litpic', 'notice', 'piclist', 'opentime', 'closetime', 'lng', 'lat', 'threetype'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Toilet::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'webid' => $this->webid,
            'aid' => $this->aid,
            'shownum' => $this->shownum,
            'addtime' => $this->addtime,
            'modtime' => $this->modtime,
            'ishidden' => $this->ishidden,
            'opentime' => $this->opentime,
            'closetime' => $this->closetime,
            'satisfyscore' => $this->satisfyscore,
            'usecount' => $this->usecount,
            'finaldestid' => $this->finaldestid,
            'issmarty' => $this->issmarty,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'seotitle', $this->seotitle])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'keyword', $this->keyword])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'tagword', $this->tagword])
            ->andFilterWhere(['like', 'litpic', $this->litpic])
            ->andFilterWhere(['like', 'notice', $this->notice])
            ->andFilterWhere(['like', 'piclist', $this->piclist])
            ->andFilterWhere(['like', 'lng', $this->lng])
            ->andFilterWhere(['like', 'lat', $this->lat])
            ->andFilterWhere(['like', 'threetype', $this->threetype]);

        return $dataProvider;
    }
}
